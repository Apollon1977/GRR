<?php
/*
 * traitementcontact.php
 * envoie l'email suite au formulaire
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2020-01-17 14:30$
 * @author    Laurent Delineau & JeromeB & Yan Naessens
 * @copyright Copyright 2003-2020 Team DEVOME - JeromeB
 * @link      http://www.gnu.org/licenses/licenses.html
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */
$grr_script_name = "traitementcontact.php";

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/functions.inc.php";
include "include/misc.inc.php";
include "include/$dbsys.inc.php";
include "include/mrbs_sql.inc.php";
include "phpmailer/class.phpmailer.php";

// Settings
require_once("./include/settings.class.php");
if (!Settings::load())
	die("Erreur chargement settings");
$msg_erreur = "Erreur. Les champs suivants doivent être obligatoirement
remplis :<br/><br/>";
$msg_ok = "Votre demande a bien été prise en compte.";
$message = "";
//~ 
if (empty($_POST['nom']))
	$message .= "Votre nom";
if (empty($_POST['prenom']))
	$message .= "Votre prénom<br/>";
if (empty($_POST['email']))
	$message .= "Votre adresse email<br/>";
if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) )
	$message .= "Adresse email non valide<br />";
if (empty($_POST['subject']))
	$message .= "Le sujet de votre demande<br/>";
if (empty($_POST['area']))
	$message .= "Le domaine n'est pas rempli<br/>";
if (empty($_POST['room']))
	$message .= "Aucune salle de choisie<br/>";
if (empty($_POST['jours']))
	$message .= "Aucun jour choisi <br/>";
if (empty($_POST['mois']))
	$message .= "Aucun mois choisi <br/>";
if (empty($_POST['année']))
	$message .= "Aucune année choisie <br/>";
if (empty($_POST['duree']))
	$message .= "Aucune durée choisie <br/>";
// ici ajouter
// recherche si le créneau est libre
// préréserve le créneau sous forme d'une réservation à modérer
// traitement des erreurs
$message = "ok?";
//if ($message != "")
//{
	$message = $msg_erreur.$message; 
	echo "<br />".$message;
	affiche_pop_up($message);
	//die();
//}

foreach ($_POST as $index => $valeur)
	$index = stripslashes(trim($valeur));
// $mail_entete n'est plus utilisé, phpmailer s'en charge
/* $mail_entete  = "MIME-Version: 1.0\r\n";
$mail_entete .= "From: {$_POST['nom']} "
."<{$_POST['email']}>\r\n";
$mail_entete .= 'Reply-To: '.$_POST['email']."\r\n";
$mail_entete .= 'Content-Type: text/plain; charset="iso-8859-1"';
$mail_entete .= "\r\nContent-Transfer-Encoding: 8bit\r\n";
$mail_entete .= 'X-Mailer:PHP/' . phpversion()."\r\n"; */

$DE = $_POST['email']; // a été filtrée

$mail_corps  = "<html><head></head><body> Message de :" .$_POST['prenom']." " .$_POST['nom'] . "<br/>";
$mail_corps  .= "Email : ".$_POST['email']. "<br/>";
$mail_corps  .= "Téléphone : ".$_POST['telephone']. "<br/><br/>";
$mail_corps  .= "<b> Sujet de la réservation :".$_POST['sujet']. "</b><br/><br/>";

$id = $_POST['area'] ;
$sql_areaName = "SELECT area_name FROM ".TABLE_PREFIX."_area where id = \"$id\" ";
$res_areaName = grr_sql_query1($sql_areaName);
$mail_corps  .= "Domaine : ".$res_areaName. "<br/> ";
$mail_corps  .= "Salle : ".$_POST['room']. "<br/><br/>";
$mail_corps  .= "Date  :".$_POST['start_day']."/".$_POST['start_month']."/".$_POST['start_year']. " <br/>";
$mail_corps  .= "Heure réservation  : ".$_POST['heure']. "h  ".$_POST['minutes']. "min<br/>";
$mail_corps  .= "Durée de la réservation : ".$_POST['duree'];
$mail_corps  .= " h ".$_POST['dureemin']. " \n</body></html>";
// ici insérer un lien de validation, cf functions.inc.php, ligne 2964
$sujet ="Réservation d'une salle";
$destinataire = Settings::get("mail_destinataire");

require_once 'phpmailer/PHPMailerAutoload.php';
require_once 'include/mail.class.php';

Email::Envois($destinataire, $sujet, $mail_corps, $DE, '', '');

// retour vers la page d'accueil
$link = page_accueil();
header('Location: '.$link);
?>