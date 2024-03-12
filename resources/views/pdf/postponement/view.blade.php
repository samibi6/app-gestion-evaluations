<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Inclure Tailwind CSS -->
    <link href="../../css/app.css" rel="stylesheet">
    <?php
    $i = 0;
    $critn = 0;
    $n = 0;
    $cote = 0;
    $finalD = 0;
    $critot = 0;
    $critPass = 0;
    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    ?>
</head>
<style>
    body {
        font-family: 'Times New Roman', Times, serif;
        /* font-family: Arial, Helvetica, sans-serif;*/
        font-size: 12px;
        padding: 0;
        margin: 0;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin-bottom: .3em;
    }

    table,
    tr,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .p05 {
        padding: 0 .5em;
    }

    .p1 {
        padding: 0 1em;
    }

    img {
        height: 4em;
        padding: 1em;
    }

    .t-1,
    .t-2 {
        border: 1px none white;
        padding: 0;
        border-collapse: collapse;
    }

    .header-coords {

        text-align: center;
        font-weight: bold;
        font-size: 1.4em;
    }

    .empty-space {
        border: none;
        height: 1.5em;
    }

    .triple-checkbox td {
        width: 33%;
        font-size: 1.4em;
        border-top: none;
        border-bottom: none;
    }

    .triple-checkbox tr {
        border-top: none;
        border-bottom: none;
    }

    .triple-checkbox {
        border-top: none;
        border-bottom: none;
    }

    .empty-space-2 {
        border: none;

    }

    .empty-space-2 div {
        border: none;

        font-size: 1.4em;
        margin-top: 2em;
        margin-bottom: 1em;

    }

    .student {
        width: 100%;
    }

    .data {
        font-family: Arial, Helvetica, sans-serif;

    }
</style>

<body>
    <main>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <img src="<?php echo $_SERVER['DOCUMENT_ROOT'] . '/images/logoIfosup.svg'; ?>" alt="">
                </td>
                <td colspan="2" class="p1">
                    <ul class="header-coords">
                        <li>Rue de la Limite, 6
                        </li>
                        <li>1300 WAVRE</li>
                        <li>010/22.20.26</li>
                    </ul>
                </td>
            </tr>
        </table>
        <table style="width: 100%;" class="empty-space">

        </table>
        <table style="width: 100%;border-bottom:none;">
            <tr style="font-weight: bold;border-bottom:none; ">
                <td style=" padding : 0.5em; font-size: 2.2em; text-align:center; width:100%;border-bottom:none;">
                    Motivation d'une décision d'ajournement</td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%;  padding: 0.5em; text-align:center;font-weight:bold;font-size:1.4em">Année
                    académique</td>
                <td><span class="data" style="margin-left: 1em; font-size: 1.4em;">{{ $schoolYear }}</span></td>
            </tr>
            <tr>
                <td style="width: 25%;  padding: 0.5em; text-align:center;font-weight:bold;font-size:1.4em">Date de
                    l'épreuve</td>
                <td><span class="data"
                        style="margin-left: 1em; font-size: 1.4em;">{{ date('d/m/Y', strtotime($courseStudentData->adjournment_exam_date)) }}</span>
                </td>

            </tr>
            <tr>
                <td style="width: 25%;  padding: 0.5em; text-align:center;font-weight:bold;font-size:1.4em">Date de
                    délibération</td>

                <td><span class="data"
                        style="margin-left: 1em; font-size: 1.4em;">{{ date('d/m/Y', strtotime($courseStudentData->date_eval)) }}</span>
                </td>
            </tr>
            <tr>
                <td style="width: 25%;  padding: 0.5em; text-align:center;font-weight:bold;font-size:1.4em">UE -
                    Intitulé + Code du DP</td>
                <td><span class="data"
                        style="margin-left: 1em; font-size: 1.4em;">{{ $course->name . '   -     ' . $course->code }}</span>
                </td>
            </tr>
        </table>
        <table class="triple-checkbox" style="width: 100%; text-align:center;">
            <tr>
                <td><input type="checkbox" {{ $courseStudentData->is_determining ? 'checked' : '' }}
                        style="vertical-align: -3px;">
                    <span style="margin-left: 0.4em;">UE déterminante</span>
                </td>
                <td><input type="checkbox" {{ $course->is_tfe ? 'checked' : '' }} style="vertical-align: -3px;"><span
                        style="margin-left: 0.4em;">Épreuve intégrée</span></td>
                <td><input type="checkbox" {{ $courseStudentData->is_other ? 'checked' : '' }}
                        style="vertical-align: -3px;"><span style="margin-left: 0.4em;">Autre</span></td>
            </tr>

        </table>
        <table class="student">
            <tr>
                <td rowspan="2" style="width:25%;font-size:1.4em;font-weight:bold;text-align:center; ">Étudiant.e
                </td>

                <td style="padding : 0.8em; font-size : 1.4em;">Nom : <span class="data">
                        {{ $student->last_name }}</span></td>
            </tr>
            <tr>
                <td style="padding : 0.8em; font-size : 1.4em;">Prénom : <span class="data">
                        {{ $student->first_name }}</span></td>
            </tr>
            </tr>
        </table>


        <table style="width: 100%;" class="empty-space-2">
            <div>L'étudiant.e est <span style="text-decoration: underline; font-weight : bold;">ajourné.e</span> pour
                le(s) motif(s) suivant(s)<sup>1</sup> :</div>
        </table>

        <table style="width:100%;" class="blunders">
            <tr>
                <td>
                    <div style="font-size: 1.4em;margin-left:2em;">-     fraude, plagiat ou non-citation des sources
                        dans une épreuve certificative</div>
                    <div
                        style="font-size:1.4em;font-style:italic;text-decoration:underline;margin-left:0.5em;margin-top:1em;margin-bottom:1em;">
                        Justification et explication :</div>
                    <div class="data" style="margin-left: 1em; font-size:1.4em;">
                        {{ $courseStudentData->adjournment_blunder_1_justification ? "$courseStudentData->adjournment_blunder_1_justification" : '' }}
                    </div>
                </td>
                <td style="width:8%;"><input type="checkbox"
                        {{ $courseStudentData->adjournment_blunder_1 ? 'checked' : '' }}
                        style="margin-top: -2em;margin-left:1em;"></td>
            </tr>
            <tr>
                <td>
                    <div style="font-size: 1.4em; margin-left: 2em;">
                        -     absence(s) valablement justifiée(s) à l' (aux) épreuve(s) permettant de vérifier
                        <div style="margin-left: 1.6em;">la maîtrise des acquis d'apprentissage</div>
                    </div>
                </td>
                <td style="width:8%;"><input type="checkbox"
                        {{ $courseStudentData->adjournment_blunder_2 ? 'checked' : '' }} style=";margin-left:1em;">
                </td>
            </tr>
        </table>
        <div style="position:absolute;bottom:-1em;">
            <div style="font-size:1.2em;text-justify:inter-word;"><sup>1</sup> Base légale : Décret du 16/1991
                organisant l'enseignement de promotion sociale - AGCF portant règlement général des études de
                l'enseignement secondaire de promotion sociale du 02-09-2015 - AGCF portant règlement général des études
                de l'enseignement supérieur de promotion sociale de type court et de régime 1 du 20-07-1993-Circulaire
                n°4711 du 09/05/2019- ROI de l'IFOSUP</div>


            <div style="margin-top: 1em; font-size: 1.4em;">Document émis le :<span class="data"
                    style="margin-left:.4em;"><?php echo $formatter->format(time()); ?></span></div>
        </div>
    </main>
</body>
