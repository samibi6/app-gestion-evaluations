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

function formatDate(dateEval, end) {
    if (dateEval === null) {
        return "Pas évalué";
    } else {
        let start = new Date(dateEval);
        // let end = new Date();
        let difference = end - start;

        //Arrange the difference of date in days, hours, minutes, and seconds format
        let days = Math.floor(difference / (1000 * 60 * 60 * 24));
        let hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((difference % (1000 * 60)) / 1000);
        hours = hours - 1;
        if (days == 0 && hours == 0 && minutes == 0) {
            return "Evalué il y a: " + seconds + " secondes";
        } else if (days == 0 && hours == 0) {
            return "Evalué il y a: " + minutes + " minutes";
        } else if (days == 0) {
            return "Evalué il y a: " + hours + " heures";
        } else {
            return "Evalué il y a: " + days + " jours";
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

console.log(props.course.proficiencies);
</script>
<template>
    <AppLayout>
        <div class="bg-white flex flex-wrap m-5 w-fit mx-auto">
            <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2 m-5 border border-zinc-300"
                :href="route('evals.index')">Retour</a>
            <ul class="block w-fit border border-slate-500 p-5 m-5 h-fit">
                <li v-for="student in students">{{ student.first_name + " " + student.last_name + " " }}
                    <span :class="textColorClass(student.id)">
                        {{ formattedDate(student.id) }}
                    </span>
                    <a class="cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                        :href="route('evals.edit', { courseId: course.id, studentId: student.id, sectionId: section })">Evaluer</a>
                </li>
            </ul>
            <ul class="w-[800px] flex flex-wrap border border-red-300">
                <li class="w-fit font-bold border border-slate-950 p-5 m-5" v-for="aptitude in course.aptitudes">{{
                    aptitude.id + ". " + aptitude.description }}
                    <ul class="mb-2">
                        <li class="font-normal ml-11 mt-5" v-for="criteria in aptitude.criterias">{{
                            criteria.id + ". " + criteria.description
                        }}
                        </li>
                    </ul>
                </li>

            </ul>
            <ul class="border border-slate-950 p-5 m-5 w-fit h-fit">
                <li v-for="proficiency in course.proficiencies">{{ proficiency.criteria_skill }}</li>
            </ul>
        </div>
    </AppLayout>
</template>
