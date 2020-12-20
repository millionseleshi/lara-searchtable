<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDoc;
use App\Models\Product;
use App\Models\User;
use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    /*
     * Do you mean search
     * */
    public function simpleSearch(Request $request)
    {
        //validate only word is accepted
        $request->validate([
            'search' => ['required', 'min:1'],
        ]);

        $query = $request->input('search');

        if ($this->suggestionResult($query)->count() > 0) {
            $response = [
                "do_you_mean" => true,
                "result" => $this->paginateCollection($this->suggestionResult($query), 30),
                "count" => $this->suggestionResult($query)->count()
            ];
            return $response;
//            return view('welcome', compact('response'));
        } else {
            //no match is found
            $message = "We can't find anything that match with: " . $query;
            return $message;
//            return view('welcome', compact('message'));
        }

    }


    /*
   * Collect suggested result
   * */
    public function suggestionResult($query)
    {
        $table_name = $this->sortSuggestions($query);

        $response = array();
        foreach ($table_name as $value) {
            if ($value == "products") {
                $response[] = $this->productSuggestions($query);
            }
            if ($value == "application_docs") {
                $response[] = $this->applicationDocSuggestions($query);
            }
            if ($value == "users") {
                $response[] = $this->userSuggestions($query);
            }

        }
        return collect($response)->flatten();
    }


    /*
    * Sort all tables suggested accordingly to the highest similarity
    *
     * @param $query
     * @return array
     */
    public function sortSuggestions($query)
    {

        $product = collect($this->productSuggestions($query))->map(function ($collection) {
            return array(
                $collection['form_distance'],
                $collection['strength_distance'],
                $collection['reference_drug_distance'],
                $collection['application_number_distance'],
                $collection['drug_name_distance'],
                $collection['active_ingredient_distance'],
            );
        })->flatten()->filter()->all();

        rsort($product);

        $application_docs = collect($this->applicationDocSuggestions($query))->map(function ($collection) {
            return array(
                $collection['doc_type_distance'],
                $collection['submission_type_distance'],
                $collection['title_distance'],
                $collection['doc_url_distance'],
            );
        })->flatten()->filter()->all();

        rsort($application_docs);

        $user = collect($this->userSuggestions($query))->map(function ($collection) {
            return array(
                $collection['name_distance'],
                $collection['email_distance'],
            );
        })->flatten()->filter()->all();

        rsort($user);

        $search_distance = [
            "products" => $product[0] ?? isset($product[0]),
            "application_docs" => $application_docs[0] ?? isset($application_docs[0]),
            "users" => $user[0] ?? isset($user[0])
        ];

        arsort($search_distance);
        return array_keys($search_distance);

    }


    /**
     * @param $query
     * @return array
     * Search Product table
     */
    private function productSuggestions($query)
    {
        $compare = new Compare();
        $product = Product::all()->filter(function ($collection) use ($query, $compare) {
            $collection['form_distance'] = $compare->jaroWinkler($query, $collection['form']);
            $collection['strength_distance'] = $compare->jaroWinkler($query, $collection['strength']);
            $collection['reference_drug_distance'] = $compare->jaroWinkler($query, $collection['reference_drug']);
            $collection['application_number_distance'] = $compare->jaroWinkler($query, $collection['application_number']);
            $collection['drug_name_distance'] = $compare->jaroWinkler($query, $collection['drug_name']);
            $collection['active_ingredient_distance'] = $compare->jaroWinkler($query, $collection['active_ingredient']);

            $similarity = 0.5;
            if (
                $collection['form_distance'] > $similarity ||
                $collection['strength_distance'] > $similarity ||
                $collection['reference_drug_distance'] > $similarity ||
                $collection['application_number_distance'] > $similarity ||
                $collection['drug_name_distance'] > $similarity ||
                $collection['active_ingredient_distance'] > $similarity
            ) {
                return $collection;
            } else {
                return [];
            }
        })->sortByDesc(function ($collection) {
            $distance_value = array(
                "form_distance" => $collection['form_distance'],
                "strength_distance" => $collection['strength_distance'],
                "reference_drug_distance" => $collection['reference_drug_distance'],
                "application_number_distance" => $collection['application_number_distance'],
                "drug_name_distance" => $collection['drug_name_distance'],
                "active_ingredient_distance" => $collection['active_ingredient_distance']
            );
            $sort_by = array_keys($distance_value, max($distance_value));
            return $collection[$sort_by[0]];
        })->transform(function ($collection) {
            $collection['result_from'] = "products";
            return $collection;
        });
        return $product->values()->all();
    }

    /**
     * @param $query
     * @return array
     * Search Application Doc
     */
    private function applicationDocSuggestions($query)
    {
        $compare = new Compare();
        $application_doc = ApplicationDoc::all()->filter(function ($collection) use ($query, $compare) {
            $collection['doc_type_distance'] = $compare->jaroWinkler($query, $collection['doc_type']);
            $collection['submission_type_distance'] = $compare->jaroWinkler($query, $collection['submission_type']);
            $collection['title_distance'] = $compare->jaroWinkler($query, $collection['title']);
            $collection['doc_url_distance'] = $compare->jaroWinkler($query, $collection['doc_url']);
            $similarity = 0.5;
            if (
                $collection['doc_type_distance'] > $similarity ||
                $collection['submission_type_distance'] > $similarity ||
                $collection['title_distance'] > $similarity ||
                $collection['doc_url_distance'] > $similarity
            ) {
                return $collection;
            } else {
                return [];
            }
        })->sortByDesc(function ($collection) {
            $distance_value = array(
                "doc_type_distance" => $collection['doc_type_distance'],
                "submission_type_distance" => $collection['submission_type_distance'],
                "title_distance" => $collection['title_distance'],
                "doc_url_distance" => $collection['doc_url_distance'],
            );
            $sort_by = array_keys($distance_value, max($distance_value));
            return $collection[$sort_by[0]];
        })->transform(function ($collection) {
            $collection['result_from'] = "application_docs";
            return $collection;
        });
        return $application_doc->values()->all();
    }

    /**
     * @param $query
     * @return array
     * Search User table
     */
    private function userSuggestions($query)
    {
        $compare = new Compare();
        $user = User::all()->filter(function ($collection) use ($query, $compare) {

            $collection['name_distance'] = $compare->jaroWinkler($query, $collection['name']);
            $collection['email_distance'] = $compare->jaroWinkler($query, $collection['email']);

            $similarity = 0.5;
            if ($collection['name_distance'] > $similarity || $collection['email_distance'] > $similarity) {
                return $collection;
            } else {
                return [];
            }
        })->sortByDesc(function ($collection) {
            $distance_value = array(
                "name_distance" => $collection['name_distance'],
                "email_distance" => $collection['email_distance']);
            $sort_by = array_keys($distance_value, max($distance_value));
            return $collection[$sort_by[0]];
        })->transform(function ($collection) {
            $collection['result_from'] = "users";
            return $collection;
        });
        return $user->values()->all();

    }

    /*
     * Paginate a collection of result
     * @param $collection
     * @param $perPage
     * @param string $pageName
     * @param null $fragment
     * @return LengthAwarePaginator
     */
    public function paginateCollection($collection, $perPage, $pageName = 'page', $fragment = null)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage);
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'pageName' => $pageName,
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
                'fragment' => $fragment
            ]
        );

        return $paginator;
    }
}
