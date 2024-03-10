<script setup>
import { ref, computed } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'students', 'section', 'dateEval', 'sectionData']);

const end = ref(new Date()); // initialiser avec la date actuelle

let i = 0;
let n = 0;

function is_PdfRenderable(studentId) {
    i++;
    formatDate(props.dateEval[studentId], end.value) === 'Pas évalué' ? '' : n++;
}
//console.log(props.students);
// Calculate values of i and n
for (const student of props.students) {
    is_PdfRenderable(student.id);
}

/*function renderPdf() {
    if (i !== n) {
        return false;
    } else {
        return true;
    }
}*/

// function renderPdf() {
//     return evaluatedStudentsCount === props.students.length;
// }
function getAcademicYearDates() {
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    let startAcademicYear, endAcademicYear;

    // Define your institution's academic year logic here
    if (currentDate.getMonth() >= 8) {
        // If it's September or later, the academic year starts in the current year
        startAcademicYear = new Date(currentYear, 8, 1); // September 1st
        endAcademicYear = new Date(currentYear + 1, 7, 31); // August 31st of next year
    } else {
        // If it's earlier than September, the academic year started in the previous year
        startAcademicYear = new Date(currentYear - 1, 8, 1); // September 1st of last year
        endAcademicYear = new Date(currentYear, 7, 31); // August 31st of current year
    }

    return { start: startAcademicYear, end: endAcademicYear };
}

let evaluatedStudentsCount = 0;

function countEvaluatedStudents() {
    const { start, end } = getAcademicYearDates();

    evaluatedStudentsCount = props.students.filter(student => {
        const evaluationDate = new Date(props.dateEval[student.id]);
        return evaluationDate >= start && evaluationDate <= end;
    }).length;
}



countEvaluatedStudents();




function formatDate(dateEval, end) {
    if (dateEval === null) {
        return "Pas évalué";
    } else {
        let start = new Date(dateEval);
        let difference = start - end;

        if (difference > 0) {
            return "Évalué dans le futur par le Doc Brown !";
        }

        let days = Math.floor(Math.abs(difference) / (1000 * 60 * 60 * 24));
        let hours = Math.floor((Math.abs(difference) % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((Math.abs(difference) % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((Math.abs(difference) % (1000 * 60)) / 1000);

        if (days === 0 && hours === 0 && minutes === 0) {
            return "Évalué il y a : " + (seconds === 1 ? "1 seconde" : seconds + " secondes");
        } else if (days === 0 && hours === 0) {
            return "Évalué il y a : " + (minutes === 1 ? "1 minute" : minutes + " minutes");
        } else if (days === 0) {
            return "Évalué il y a : " + (hours === 1 ? "1 heure" : hours + " heures");
        } else {
            return "Évalué il y a : " + (days === 1 ? "1 jour" : days + " jours");
        }
    }
}


const textColorClass = (studentId) => {
    const evaluationDate = new Date(props.dateEval[studentId]);
    const elapsedTime = end.value - evaluationDate;
    const { start, end: endAcademicYear } = getAcademicYearDates();

    if (evaluationDate < start || evaluationDate > endAcademicYear) {
        return 'text-red-500'; // dates en dehors de l'année scolaire actuelle
    } else if (Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) < 12) { // moins de 12 heures
        return 'text-green-600'; // vert pour récent
    } else if (Math.floor(elapsedTime / (1000 * 60 * 60 * 24)) < 14) { // moins de deux semaines 
        return 'text-orange-500'; // orange pour récent mais pas trop 
    } else {
        return 'text-orange-700'; // orange foncé pour pas récent
    }
};


// Calculateur de propriété pour formater la date en fonction de la date actuelle (end)
const formattedDate = computed(() => {
    return (studentId) => formatDate(props.dateEval[studentId], end.value);
});

// Mettre à jour la date actuelle (end) toutes les secondes
setInterval(() => {
    end.value = new Date();
}, 1000);

const isPdfGeneratable = (studentId) => {
    const evaluationDate = new Date(props.dateEval[studentId]);
    const { start, end } = getAcademicYearDates();
    return evaluationDate >= start && evaluationDate <= end;
};
</script>

<template>
    <AppLayout title="Évaluations">
        <div class="bg-gray-300 border border-gray-400 mx-auto w-1/2 p-5 rounded-md shadow-xl mt-12">
            <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition inline-block px-4 py-2 mb-5 border border-red-500 rounded-md"
                :href="route('evals.index')">Retour</a>
            <div class="text-center font-bold text-lg mb-4">Étudiants de l'UE <span class="text-gray-700">{{
                course.name }}</span>
                dans la section <span class="text-gray-700">{{
                    sectionData.name }}</span></div>

            <ul class="divide-y divide-gray-300">
                <li v-for="student in   students  " :key="student.id" class="flex items-center justify-between py-4">
                    <div>
                        <span class="font-semibold">{{ student.first_name + ' ' + student.last_name }}</span>
                        <span :class="textColorClass(student.id)" class="ml-2">
                            {{ formattedDate(student.id) }}
                        </span>
                    </div>

                    <div>
                        <a :href="route('evals.edit', { courseId: course.id, studentId: student.id, sectionId: section })"
                            class="text-blue-500 hover:bg-blue-500 hover:text-white transition inline-block px-4 py-2 mr-2 rounded-md">Evaluer</a>

                        <a :class="[!isPdfGeneratable(student.id) ? 'rounded-md text-zinc-500 cursor-not-allowed' : ' text-blue-500 hover:text-white hover:bg-blue-500 transition rounded-md', 'inline-block px-4 py-2 ']"
                            :href="!isPdfGeneratable(student.id) ? '' : route('pdf.generate', { course: course.id, section: section, student: student.id })">
                            Générer PDF
                        </a>
                    </div>
                </li>

                <li class="py-4 text-center ">
                    <a :class="[evaluatedStudentsCount === 0 ? 'text-zinc-500 rounded-md cursor-not-allowed inline-block px-4 py-2 block' : 'text-blue-500 rounded-md hover:text-white hover:bg-blue-500 transition inline-block px-4 py-2 block']"
                        :href="evaluatedStudentsCount === 0 ? '' : route('pdf.generate', { course: course.id, section: section })">
                        Générer les PDF pour tous les étudiants
                    </a>

                </li>
            </ul>
        </div>
    </AppLayout>
</template>
  
  

            <!-- <ul class="w-[800px] flex flex-wrap border border-red-300">
                <li class="w-fit font-bold border border-slate-950 p-5 m-5" v-for=" aptitude  in  course.aptitudes ">{{
                    aptitude.id + ". " + aptitude.description }}
                    <ul class="mb-2">
                        <li class="font-normal ml-11 mt-5" v-for=" criteria  in  aptitude.criterias ">{{
                            criteria.id + ". " + criteria.description
                        }}
                        </li>
                    </ul>
                </li>

            </ul>
            <ul class="border border-slate-950 p-5 m-5 w-fit h-fit">
                <li v-for=" proficiency  in  course.proficiencies ">{{ proficiency.criteria_skill }}</li>
            </ul>-->