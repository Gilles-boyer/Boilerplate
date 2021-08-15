# Function filter Data en get()
> Cette function permet d'utiliser une route en get pour créer une requête sur la BDD via le model et return le resultat de la recherche s'il exist. Cette fonction est utiliser dans laravel 8.La function vérifie les parametre présent dans la requete pour savoir quoi faire.

## Une route get() dans laravel 
```php
Route::get("/filter", [FilterRequestDataController::class, 'filterDataRequest']);
```
http://127.0.0.1:8000/filter?model=user&select=id,last_name,first_name
> la base de l'adresse est : http://127.0.0.1:8000/filter
> le point "?" démare la requete get(), les parametres aprés le point d'intérogation constitut les éléments de recherche dans la BDD.
> le "&" separe les différents parametres

## 1er Parametre => model
ex : model=user
> il est important de mettre aucune autre annotation comme dans l'exemple, la function va vérifier si un model exist et va retourner les informations contenu dans la table du model, sans model correct la function s'arrete et return une erreur

## Les Parametres en Option

### select
ex : select=id,last_name,first_name
> le select selectionne les champs à afficher dans le resultat, les champs sont traités dans un [], si le champs n'existe pas la function s'arrete et return une erreur
```php
    if ($request->select) {
        $model = $model::select($selectFields['field']);
    } else {
        $model = $model::select("*");
    }
```
### where ou orwhere
ex : where=id,=,1;last_name,=,test
ex : orwhere=id,=,3
> le where ou le orwhere va appliquer une condition pour filtrer l'affichage, on peut poser plusieur condition dans le where pour séparer une condition d'une autre nous allons utiliser ";" la condition s'écrit : 1,=,2 . le 1 est le champ de la table à filtrer, le = design le type de filtre (=,>,<,>=,<=), et le 2 est la valeur recherchée. les virgules "," sépare les parties. le where ou orwhere applique le filtre sur le model.
```php
    $model = $model->where($whereFields['field']);
```
