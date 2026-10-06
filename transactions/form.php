<?php

/*
 * Formulaire partagé par add.php et edit.php.
 * Variables attendues : $transaction (tableau), $categories, $submitLabel
 */

if(!isset($transaction)){
    exit();
}

?>

<form method="POST">

<?= csrfField() ?>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Titre</label>

<input
type="text"
class="form-control"
name="title"
maxlength="100"
placeholder="Ex : Courses, Salaire..."
value="<?= e($transaction["title"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Montant (<?= e($_SESSION["currency"] ?? "€") ?>)</label>

<input
type="number"
step="0.01"
min="0.01"
class="form-control"
name="amount"
value="<?= e($transaction["amount"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Type</label>

<select
class="form-select"
name="type"
id="type">

<option value="expense" <?= $transaction["type"]=="expense" ? "selected" : "" ?>>💸 Dépense</option>

<option value="income" <?= $transaction["type"]=="income" ? "selected" : "" ?>>💰 Revenu</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Catégorie</label>

<select
class="form-select"
name="category"
id="category"
required>

<?php

$found=false;

foreach($categories as $cat){

    $selected=$transaction["category"]==$cat["name"];

    $found=$found || $selected;

?>

<option
value="<?= e($cat["name"]) ?>"
data-type="<?= e($cat["type"] ?? "") ?>"
<?= $selected ? "selected" : "" ?>>

<?= e($cat["name"]) ?>

</option>

<?php } ?>

<?php if(!$found && $transaction["category"]!=""){ ?>

<option value="<?= e($transaction["category"]) ?>" selected><?= e($transaction["category"]) ?></option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Date</label>

<input
type="date"
class="form-control"
name="transaction_date"
value="<?= e($transaction["transaction_date"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Notes</label>

<input
type="text"
class="form-control"
name="notes"
placeholder="Facultatif"
value="<?= e($transaction["notes"]) ?>">

</div>

</div>

<button
type="submit"
class="btn btn-success"
name="save">

💾 <?= e($submitLabel) ?>

</button>

<a
href="index.php"
class="btn btn-secondary">

Annuler

</a>

</form>

<script>

/* Affiche uniquement les catégories correspondant au type choisi */

const typeSelect=document.getElementById("type");
const categorySelect=document.getElementById("category");

function filterCategories(){

    categorySelect.querySelectorAll("option").forEach(option=>{

        const type=option.dataset.type;

        option.hidden=type!==undefined && type!=="" && type!==typeSelect.value;

    });

    if(categorySelect.selectedOptions[0] && categorySelect.selectedOptions[0].hidden){

        const first=[...categorySelect.options].find(option=>!option.hidden);

        if(first){
            first.selected=true;
        }

    }

}

typeSelect.addEventListener("change",filterCategories);

filterCategories();

</script>
