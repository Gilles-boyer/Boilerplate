<?php

namespace App\Http\Controllers;

use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class FilterRequestDataController extends Controller
{

    function filterDataRequest(Request $request)
    {
        //verify $request
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $model = $this->checkAndReturnModel($request->model);
        if (!($model::exists())) {
            return [
                'error' => "le model n'existe pas"
            ];
        }

        //check select
        if ($request->select) {
            $selectFields = $this->checkAttributExist($request->select, $request->model);

            if ($selectFields['errors']) {
                return $selectFields['errors'];
            }

            $model = $model::select($selectFields['field']);
        } else {
            $model = $model::select("*");
        }

        //check where
        if ($request->where) {
            $dataWhere = explode(';', $request->where);
            $whereFields = $this->checkAttributExist($dataWhere, $request->model);

            if ($whereFields['errors']) {
                return $whereFields['errors'];
            }

            $model = $model->where($whereFields['field']);
        }

        //check orwhere
        if ($request->orwhere) {
            $dataWhere = explode(';', $request->orwhere);
            $whereFields = $this->checkAttributExist($dataWhere, $request->model);

            if ($whereFields['errors']) {
                return $whereFields['errors'];
            }

            $model = $model->orWhere($whereFields['field']);
        }

        //check like
        if ($request->wherelike) {
            $dataLike = explode('=', $request->wherelike);
            $likeFields = $this->checkAttributExist($dataLike[0], $request->model);

            if ($likeFields['errors']) {
                return $likeFields['errors'];
            }

            $model = $model->whereLike($likeFields['field'], $dataLike[1]);
        }

        //check orlike
        if ($request->orwherelike) {
            $dataLike = explode('=', $request->orwherelike);
            $likeFields = $this->checkAttributExist($dataLike[0], $request->model);

            if ($likeFields['errors']) {
                return $likeFields['errors'];
            }

            $model = $model->orWhereLike($likeFields['field'], $dataLike[1]);
        }

        //check with
        if ($request->with) {

            $withs = $this->checkRelationExist($request->with, $model);

            if ($withs['errors']) {
                return $withs['errors'];
            }

            $model = $model->with($withs['field']);
        }

        //check first
        if ($request->first) {
            return $model->first();
        }

        //check orderby
        if ($request->orderby) {
            $orderBy = explode(",",$request->orderby);

            if($orderBy[1]){
                $order = $this->goodDataString($orderBy[1]);
            } else {
                $order = "asc";
            }

            $orderByField = $this->checkAttributExist($orderBy[0], $request->model);

            if ($orderByField['errors']) {
                return $orderByField['errors'];
            }

            $model = $model->orderBy($orderByField['field'][0], $order);
        }

        //paginate
        if ($request->paginate > 0) {
            return $model->paginate($request->paginate + 0);
        }

        return $model->get();
    }

    function goodDataString($string)
    {
        $string = trim($string);
        $string = strtolower($string);

        return $string;
    }

    function checkRelationExist($relations, $model)
    {
        $relations = explode(",", $relations);

        $listRelation = [
            'errors' => [],
            'field'  => []
        ];

        $modelTest = clone $model;

        foreach ($relations as $relation) {
            $relation = $this->goodDataString($relation);

            if (method_exists($modelTest->first(), $relation)) {
                array_push($listRelation['field'], $relation);
            } else {
                $error = "la relation " . $relation . " n'existe pas.";
                array_push($listRelation['errors'], $error);
            }
        }
        return $listRelation;
    }


    /**
     * check fields and if exist return fields and errors
     *
     * @return $listAttributes;
     */
    function checkAttributExist($attributes, $model)
    {
        if (gettype($attributes) == "array") {
            $attributesAll = [];
            foreach ($attributes as $attribut) {
                $attribut = explode(',', $attribut);
                array_push($attributesAll, $attribut);
            }

            $attributes = $attributesAll;
        } else {
            $attributes = explode(',', $attributes);
        }

        $listAttributes = [
            'errors' => [],
            'field'  => []
        ];

        $model = strtolower($model);

        foreach ($attributes as $attribute) {

            if (gettype($attribute) == "array") {
                $attribute[0] = $this->goodDataString($attribute[0]);
                $fieldExist = Schema::hasColumn($model . "s", $attribute[0]);
            } else {
                $attribute = $this->goodDataString($attribute);
                $fieldExist = Schema::hasColumn($model . "s", $attribute);
            }

            if ($fieldExist) {
                array_push($listAttributes['field'], $attribute);
            } else {
                if (gettype($attribute) == "array") {
                    $error = "le champs " . $attribute[0] . " n'existe pas.";
                } else {
                    $error = "le champs " . $attribute . " n'existe pas.";
                }
                array_push($listAttributes['errors'], $error);
            }
        }
        return $listAttributes;
    }

    /**
     * check model and if exist return model
     *
     * @return $classModel
     */
    public function checkAndReturnModel($model)
    {
        $model = $this->goodDataString($model);

        $model = ucfirst($model);

        $classModel = '\App\Models\\' . $model;

        return $classModel;
    }


    /**
     * check Request and verify data request for validate
     *
     * @return $validator with errors
     */
    function validateRequest($request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'model'        => 'required|string|max:50',
                'select'       => 'string|max:255',
                'where'        => 'string|max:255',
                'orwhere'      => 'string|max:255',
                'orwherelike'  => 'string|max:255',
                'first'        => 'boolean',
                'wherelike'    => 'string|max:255',
                'paginate'     => 'string',
                'orderby'      => 'string|max:255',
                'with'         => 'string|max:100'
            ],
            [
                'required'  => 'Le champs :attribute est requis',
                'string'    => 'Le champs :attribute n est pas un sting',
                'max'       => 'Vous avez dépassé le nombre de caratère max dans le champs :attribute ' // :attribute renvoie le champs / l'id de l'element en erreure
            ]
        );

        return $validator;
    }
}
