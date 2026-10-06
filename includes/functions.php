<?php

/* ==========================
   Session & navigation
========================== */

function isLoggedIn(){

    return isset($_SESSION["user_id"]);

}

function redirect($url){

    header("Location: ".$url);
    exit();

}

function requireLogin(){

    if(!isLoggedIn()){
        redirect("../auth/login.php");
    }

}

/* ==========================
   Affichage
========================== */

function e($value){

    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");

}

function money($amount){

    $currency = $_SESSION["currency"] ?? "€";

    return number_format((float)$amount,2,","," ")." ".$currency;

}

function alert($message,$type="success"){

    return "<div class='alert alert-$type'>".e($message)."</div>";

}

function getInitials($firstname,$lastname){

    return mb_strtoupper(mb_substr($firstname,0,1).mb_substr($lastname,0,1));

}

function monthName($month){

    $months=[1=>"Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"];

    return $months[(int)$month] ?? "";

}

function today(){

    return date("d/m/Y");

}

function now(){

    return date("d/m/Y H:i");

}

/* ==========================
   Messages flash
========================== */

function flash($message,$type="success"){

    $_SESSION["flash"]=["message"=>$message,"type"=>$type];

}

function showFlash(){

    if(empty($_SESSION["flash"])){
        return "";
    }

    $flash=$_SESSION["flash"];

    unset($_SESSION["flash"]);

    return alert($flash["message"],$flash["type"]);

}

/* ==========================
   Protection CSRF
========================== */

function csrfToken(){

    if(empty($_SESSION["csrf"])){
        $_SESSION["csrf"]=bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf"];

}

function csrfField(){

    return '<input type="hidden" name="csrf" value="'.csrfToken().'">';

}

function checkCsrf(){

    if(!hash_equals($_SESSION["csrf"] ?? "", $_POST["csrf"] ?? "")){

        http_response_code(400);

        exit("Requête invalide. Rechargez la page et réessayez.");

    }

}

/* Formulaire de suppression (POST + CSRF) */

function deleteButton($id,$confirm,$label="🗑️",$class="btn btn-danger btn-sm"){

    return '<form method="POST" action="delete.php" class="d-inline" onsubmit="return confirm(\''.e($confirm).'\')">'
        .csrfField()
        .'<input type="hidden" name="id" value="'.(int)$id.'">'
        .'<button type="submit" class="'.$class.'">'.$label.'</button>'
        .'</form>';

}

/* ==========================
   Validation
========================== */

function postAmount($key,$allowZero=false){

    $value=str_replace(",",".",trim($_POST[$key] ?? ""));

    if(!is_numeric($value)){
        return null;
    }

    $value=round((float)$value,2);

    if($value<0 || (!$allowZero && $value==0)){
        return null;
    }

    return $value;

}

function postDate($key,$required=true){

    $value=trim($_POST[$key] ?? "");

    if($value===""){
        return $required ? null : "";
    }

    $date=DateTime::createFromFormat("Y-m-d",$value);

    return ($date && $date->format("Y-m-d")===$value) ? $value : null;

}

/* ==========================
   Catégories
========================== */

function defaultCategories(){

    return [
        ["Salaire","income"],
        ["Autres revenus","income"],
        ["Courses","expense"],
        ["Logement","expense"],
        ["Transport","expense"],
        ["Restaurant","expense"],
        ["Loisirs","expense"],
        ["Santé","expense"],
        ["Shopping","expense"],
        ["Autre","expense"]
    ];

}

function userCategories($pdo,$user_id){

    $sql=$pdo->prepare("
    SELECT name,type
    FROM categories
    WHERE user_id=?
    ORDER BY name
    ");

    $sql->execute([$user_id]);

    $categories=$sql->fetchAll();

    if(count($categories)==0){

        foreach(defaultCategories() as $cat){
            $categories[]=["name"=>$cat[0],"type"=>$cat[1]];
        }

    }

    return $categories;

}

/* ==========================
   Notifications
========================== */

function notify($pdo,$user_id,$message){

    /* Évite les doublons de notifications non lues */

    $sql=$pdo->prepare("
    SELECT COUNT(*)
    FROM notifications
    WHERE user_id=? AND message=? AND is_read=0
    ");

    $sql->execute([$user_id,$message]);

    if($sql->fetchColumn()==0){

        $pdo->prepare("
        INSERT INTO notifications(user_id,message)
        VALUES(?,?)
        ")->execute([$user_id,$message]);

    }

}

/* Prévient l'utilisateur si le budget du mois de $date est dépassé */

function checkBudget($pdo,$user_id,$date){

    $month=(int)date("n",strtotime($date));
    $year=(int)date("Y",strtotime($date));

    $sql=$pdo->prepare("
    SELECT amount
    FROM budgets
    WHERE user_id=? AND month=? AND year=?
    LIMIT 1
    ");

    $sql->execute([$user_id,$month,$year]);

    $budget=$sql->fetchColumn();

    if($budget===false){
        return;
    }

    $sql=$pdo->prepare("
    SELECT COALESCE(SUM(amount),0)
    FROM transactions
    WHERE user_id=?
    AND type='expense'
    AND MONTH(transaction_date)=?
    AND YEAR(transaction_date)=?
    ");

    $sql->execute([$user_id,$month,$year]);

    $spent=$sql->fetchColumn();

    if($spent>$budget){

        notify($pdo,$user_id,"⚠️ Budget dépassé pour ".monthName($month)." $year : ".money($spent)." dépensés pour un budget de ".money($budget).".");

    }elseif($spent>=$budget*0.8){

        notify($pdo,$user_id,"🔔 Plus de 80 % du budget utilisé pour ".monthName($month)." $year.");

    }

}

/* ==========================
   Objectifs
========================== */

/* Ajoute $delta au montant épargné d'un objectif et met à jour son statut */

function adjustGoal($pdo,$user_id,$goal_id,$delta){

    $sql=$pdo->prepare("
    SELECT *
    FROM goals
    WHERE id=? AND user_id=?
    ");

    $sql->execute([$goal_id,$user_id]);

    $goal=$sql->fetch();

    if(!$goal){
        return;
    }

    $current=max(0,$goal["current_amount"]+$delta);

    $status=$current>=$goal["target_amount"] ? "Terminé" : "En cours";

    $pdo->prepare("
    UPDATE goals
    SET current_amount=?, status=?
    WHERE id=? AND user_id=?
    ")->execute([$current,$status,$goal_id,$user_id]);

    if($status=="Terminé" && $goal["status"]!="Terminé"){

        notify($pdo,$user_id,"🎉 Félicitations ! Objectif « ".$goal["title"]." » atteint.");

    }

}

/* ==========================
   Statistiques
========================== */

/* Revenus et dépenses des $count derniers mois (mois courant inclus) */

function monthlyTotals($pdo,$user_id,$count){

    $start=date("Y-m-01",strtotime("-".($count-1)." months",strtotime(date("Y-m-01"))));

    $sql=$pdo->prepare("
    SELECT DATE_FORMAT(transaction_date,'%Y-%m') ym,
           SUM(CASE WHEN type='income' THEN amount ELSE 0 END) income,
           SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) expense
    FROM transactions
    WHERE user_id=?
    AND transaction_date>=?
    GROUP BY ym
    ");

    $sql->execute([$user_id,$start]);

    $rows=[];

    foreach($sql->fetchAll() as $row){
        $rows[$row["ym"]]=$row;
    }

    $short=[1=>"Janv.","Févr.","Mars","Avr.","Mai","Juin","Juil.","Août","Sept.","Oct.","Nov.","Déc."];

    $result=["labels"=>[],"income"=>[],"expense"=>[]];

    for($i=0;$i<$count;$i++){

        $time=strtotime("+$i months",strtotime($start));

        $ym=date("Y-m",$time);

        $result["labels"][]=$short[date("n",$time)]." ".date("y",$time);
        $result["income"][]=(float)($rows[$ym]["income"] ?? 0);
        $result["expense"][]=(float)($rows[$ym]["expense"] ?? 0);

    }

    return $result;

}

/* Dépenses par catégorie, éventuellement à partir d'une date */

function expensesByCategory($pdo,$user_id,$since=null){

    $sql=$pdo->prepare("
    SELECT category,
           SUM(amount) total
    FROM transactions
    WHERE user_id=?
    AND type='expense'
    AND transaction_date>=?
    GROUP BY category
    ORDER BY total DESC
    ");

    $sql->execute([$user_id,$since ?? "1000-01-01"]);

    return $sql->fetchAll();

}
