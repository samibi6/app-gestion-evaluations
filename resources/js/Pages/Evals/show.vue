<script setup>
import { ref, computed } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'students', 'section', 'dateEval']);

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

function renderPdf() {
    if (i !== n) {
        return false;
    } else {
        return true;
    }
}

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
    const elapsedTime = end.value - new Date(props.dateEval[studentId]);
    if (Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) < 12) { // moins de 12 heures
        return 'text-green-500'; // par exemple, couleur verte pour récent
    } else if (Math.floor(elapsedTime / (1000 * 60 * 60 * 24)) < 1) { // moins de un jour
        return 'text-yellow-500'; // par exemple, couleur jaune pour récent mais pas très
    } else {
        return 'text-red-500'; // par exemple, couleur rouge pour plus ancien
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
</script>

<template>
    <AppLayout>
        <div class="py-12">
            <div class="bg-white flex flex-wrap m-5 max-w-7xl mx-auto sm:px-6 lg:px-8">

                <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2 m-5 border border-zinc-300"
                    :href="route('evals.index')">Retour</a>

                <ul class="block w-fit border mx-auto border-slate-500 p-5 m-5 h-fit">

                    <li class="w-[500px] flex items-center justify-center" v-for="student in  students ">
                        <span class="mr-3">
                            {{ student.first_name + " " + student.last_name }}
                        </span>

                        <span
                            :class="formattedDate(student.id) === 'Pas évalué' || formattedDate(student.id) === 'Évalué dans le futur par le Doc Brown !' ? 'text-red-500' : textColorClass(student.id)">
                            {{ formattedDate(student.id) }}
                        </span>

                        <span class="w-fit ml-auto inline-block">
                            <a class="ml-3 cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                                :href="route('evals.edit', { courseId: course.id, studentId: student.id, sectionId: section })">Evaluer</a>

                            <a :class="formattedDate(student.id) === 'Pas évalué' || formattedDate(student.id) === 'Évalué dans le futur par le Doc Brown !' ? 'text-zinc-500 cursor-default p-2 h-full inline-block' : 'text-green-500 font-bold hover:text-white hover:bg-green-500 transition p-2 h-full inline-block'"
                                :href="['Pas évalué', 'Évalué dans le futur par le Doc Brown !'].includes(formattedDate(student.id)) ? '#' : route('pdf.generate', {
                        course: course.id, section: section,
                        student: student.id
                    })">Générer PDF</a>
                        </span>


                    </li>

                    <a :class="renderPdf() ? 'text-indigo-500 hover:text-white hover:bg-indigo-500 transition font-bold w-fit mx-auto p-2 mt-4 block' : 'text-zinc-500 cursor-default transition w-fit mx-auto p-2 mt-4 block'"
                        :href="renderPdf() ? route('pdf.generate', {
                        course: course.id, section: section
                    }) : '#'">Générer les pdf pour toute la classe</a>

                </ul>
                <ul class="w-full grid grid-cols-3 border border-gray-300 rounded-md">
                    <li class="w-fit font-bold border border-slate-950 p-5 m-5"
                        v-for=" aptitude  in  course.aptitudes ">{{
                        aptitude.id + ". " + aptitude.description }}
                        <ul class="mb-2">
                            <li class="font-normal ml-11 mt-5" v-for=" criteria  in  aptitude.criterias ">{{
                        criteria.id + ". " + criteria.description
                    }}
                            </li>
                        </ul>
                    </li>

                </ul>
                <ul class="border border-slate-950 p-5 m-5 w-full h-fit grid grid-cols-4">
                    <li class="border-2 p-2" v-for=" proficiency  in  course.proficiencies ">
                        {{ proficiency.id + ". " + proficiency.criteria_skill }}</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
