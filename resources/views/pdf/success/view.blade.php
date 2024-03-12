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
    ?>
</head>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
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
</style>

<body>
    <main>
        <table style="width: 100%;">
            <tr>
                <td style="width: 40%;">
                    <img src="<?php echo $_SERVER['DOCUMENT_ROOT'] . '/images/logoIfosup.svg'; ?>" alt="">
                </td>
                <td colspan="2" class="p1">
                    <ul>
                        <li>Nom &amp; prénom de l'étudiant.e : {{ $student->last_name . ' ' . $student->first_name }}
                        </li>
                        <li>Section : {{ $section->name }}</li>
                        <li>Unité d'enseignement : {{ $course->name }}</li>
                        <li>Code de l'UE : {{ $course->code }}</li>
                        <li>Nom du/des chargés.es de cours :
                            @foreach ($courseUser as $cu)
                                {{ $cu->name }},
                            @endforeach
                        </li>
                        <li>Année scolaire : {{ $schoolYear }}</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; padding: .5em">POUR ATTEINDRE LE SEUIL DE REUSSITE,
                    L'ETUDIANT.E PROUVERA QU'IL/ELLE EST CAPABLE :</td>
            </tr>
            <tr>
                <td class="t-1" colspan="3">
                    <table style="width: 100%;">
                        <tr style="font-weight: bold;">
                            <td colspan="3" style="padding: 1em; padding-bottom:0; font-size: 10px; color: gray;">
                                (Indiquer
                                ici le
                                chapeau repris
                                au-dessus des acquis d'apprentissage dans le dossier pédagogique)
                                <div style="font-size: 15px; color: black;"> {{ $course->lead }}</div>
                            </td>
                            <td style="text-align: center; width: 15.5%;"> ACQUIS </td>
                            <td style="text-align: center; width: 14.5%;"> NON ACQUIS </td>
                        </tr>
                        {{-- FOREACH AA --}}
                        @foreach ($aptitudes as $aptitude)
                            <?php
                            $i++;
                            $critn = 0;
                            $decision = 0;
                            ?>
                            <tr>
                                <td style="width: 30%; padding: .5em;">AA {{ $i }}:
                                    {{ $aptitude->description }}
                                </td>
                                <td colspan="4" class="t-2" style="border: 0;">
                                    <table style="width: 100%;">
                                        {{-- FOREACH CRITERES --}}
                                        @if (isset($criteriaByApt[$aptitude->id]))
                                            @foreach ($criteriaByApt[$aptitude->id] as $criteria)
                                                <?php
                                                $critn++;
                                                $critot++;
                                                // Initialize $acquired[$criteria->id] to 0 if it's not set
                                                $acquired[$criteria->id] = isset($acquired[$criteria->id]) ? $acquired[$criteria->id] : 1;
                                                $decision += $acquired[$criteria->id] ? 1 : 0;
                                                $critPass += $acquired[$criteria->id] ? 1 : 0;
                                                ?>
                                                <tr>
                                                    <td style="width: 57.2%; padding: .5em;">
                                                        {{ $criteria->description }}</td>
                                                    <td style="text-align: center; font-weight: bold;">
                                                        {{ $acquired[$criteria->id] ? 'X' : '' }}
                                                    </td>
                                                    <td style="text-align: center; font-weight: bold;">
                                                        {{ !$acquired[$criteria->id] ? 'X' : '' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <?php $decision === $critn ? $finalD++ : ''; ?>
                                        <tr>
                                            <td
                                                style="width: 57%; font-weight: bold; text-align: center; padding: .5em;">
                                                Décision pour AA {{ $i }} : tous les critères</td>
                                            <td style="text-align: center; font-weight: bold;">
                                                {{ $decision === $critn ? 'X' : '' }}
                                            </td>
                                            <td style="text-align: center; font-weight: bold;">
                                                {{ $decision === $critn ? '' : 'X' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr style="line-height: 1.5em;">
                <td style="width: 0%; text-align: center; padding: .5em;"><span style="font-weight: bold;">Tous les AA
                        sont " acquis "<br>
                        REUSSITE </span><br> &#10132; Voir degré de maitrise</td>
                <td style="width: 33%; text-align: center; padding: .5em;"><span style="font-weight: bold;">Au moins un
                        AA non-acquis</span><span style="font-size: .5em;"> (en
                        1<span style="vertical-align: super; font-size: .7em;">ère</span> session) </span><br><span
                        style="font-weight: bold;"> AJOURNEMENT </span><br> &#10132; Voir justification ajournement</td>
                <td style="width: 33%; text-align: center; padding: .5em;"><span style="font-weight: bold;">Au moins un
                        AA non-acquis</span><span style="font-size: .5em;"> (en
                        2<span style="vertical-align: super; font-size: .7em;">ème</span> session) </span><br><span
                        style="font-weight: bold;"> REFUS </span><br> &#10132; Voir justification de refus</td>
            </tr>
            <tr>
                <td colspan="3"
                    style="font-weight: bold; text-align: center; font-size: 15px; border-top: 4px solid #718096; padding: .5em;">
                    DEGRÉ DE MAÎTRISE (% >= 50)</td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td colspan="3" style="padding: .5em; font-weight: bold; font-style: italic;">0-1 = Acquis, 2-3 =
                    Bien, 4-5 = TB, 6-7 = TTB, 8-9 = Excellent, 10 = Parfait</td>
            </tr>
            <tr>
                <th style="width: 40%;">Critères :</th>
                <th style="width: 40%;">Indicateurs :</th>
                <th style="text-align: center; width: 20%;">Niveau de maîtrise</th>
            </tr>
            <!-- FOREACH Maitrise -->
            @foreach ($proficiencies as $proficiency)
                <?php
                $n++;
                $cote += isset($acquiredProf[$proficiency->id]) ? $acquiredProf[$proficiency->id] : 0;
                $proficiencyScore = isset($acquiredProf[$proficiency->id]) ? $acquiredProf[$proficiency->id] : 0;
                if ($proficiencyScore === null && $critot === $critPass) {
                    $proficiencyScore = 0;
                }
                ?>
                <tr>
                    <td style="padding: .5em;">{{ $proficiency->criteria_skill }}</td>
                    <td style="padding: .5em;">{{ $proficiency->indicator }}</td>
                    <td style="text-align: center; padding: .5em;">
                        {{ $i === $finalD ? $proficiencyScore : '-' }} /10
                    </td>
                </tr>
            @endforeach
            <?php
            if ($critot === $critPass) {
                $result = round(($cote / $n) * 5 + 50, 1);
            } else {
                // $result = ($critPass / $critot) * 50;
                $result = 'Non évaluable';
            }
            ?>
            <tr style="font-weight: bold; background-color: lightgray;">
                <td style="padding: .5em;">RESULTAT FINAL</td>
                <td style="padding: .5em; font-size: .8em; text-align: center;">TOUS les AA sont atteints et le degré de
                    maîtrise permet de calculer la cote suivante (>= 50 %)</td>
                <td style="padding: .5em; text-align: center;">{{ $result }} /100</td>
            </tr>
            <tr>
                <td colspan="3">
                    <ul style="line-height: 15px; margin-bottom: 4em;">
                        <li>Date : <?php echo date('d/m/Y'); ?></li>
                        <li>Signature du/des chargés.es de cours :</li>
                    </ul>
                </td>
            </tr>
        </table>

    </main>
</body>
