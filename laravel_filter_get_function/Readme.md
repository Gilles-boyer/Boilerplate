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
### like ou orlike
ex : wherelike=last_name,first_name=bea
ex : orwherelike=last_name=at
> le wherelike ou le orwherelike va appliquer une recherche sur des champs pour savoir s'il contienne une partie ou la totalité de la valeur. la recherche s'ecrit : 1,1,1=2 . la partie 1 designe la liste des champs pour la recherche, le = est juste un séparateur, et la  partie 2 designe la valeur recherchée.
```php
   $model = $model->whereLike($likeFields['field'], $dataSearch);
```
### with
ex : with=role,promotion
> le with va permetre de vérifier les relations existant avec la table et les retournées. le with s'ecrit 1,1,1 :  le 1 est le nom de la method de la classe qui permet de retourné les informations de la relation, la "," est le séparateur des différentes relations.
```php
  $model = $model->with($withs['field']);
```
### first
ex : first=1
> le first va permetre d'arreter la function et de retourné le premier élément de la recherche. Deux chois possible 0 ou 1 : le 0 signifie false et le 1 true.
```php
  return $model->first();
```
### orderby
ex : orderby=first_name,asc
> le orderby va permettre de ranger le résultat en fonction d'un champs et d'un ordre. le orderby s'ecrit 1,2 : le 1 désigne le champ qui sera trié et le 2 designe le type de rangement "asc" ou "desc", par défaut le triage est asc
```php
  $model = $model->orderBy($orderByField['field'][0], $order);
```
### paginate
ex : paginate=2
> le paginate va permettre de retourner le resultat paginé. le paginate prend un seul parametre qui est un integer du nombre d'entité par page.
```php
  return $model->paginate($nbreEntité);
```

