
<!-- JUSTE CHECK SI COURS DEJA DANS SECTION, SI OUI NE PAS L'AFFICHER (c'est un formulaire d'ajout de cours a une section)
ajouter formulaire de suppression dans lequel on voit tous les cours associés à la section et possibilité de modifier l'annee du cours dans la section -->

<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
const props = defineProps(['sections', 'courses', 'coursesBySection', 'errors']);
let coursList = props.courses;
let coursArray = [];
const form = useForm({
    section: '',
    course: [],
});

const submitForm = () => {
    form.post(route('coursSections.store'));
    form.reset();
    coursList = props.courses;
};

function updateCourses() {
    coursArray = [];
    if (typeof props.coursesBySection[form.section] === 'undefined') {
        coursList = props.courses;
    } else {
        const sectionCourses = props.coursesBySection[form.section];
        const coursId = sectionCourses.map(cours => cours.id);
        coursList = props.courses;
        coursList = props.courses.filter(course => !coursId.includes(course.id));
        // for (const key in props.coursesBySection[form.section]) {
        //     coursArray += props.coursesBySection[form.section][key].id;
        // }
    }
}

let add = ref(false);
</script>

<template>
    <button class="bg-blue-500 text-white font-bold p-3 rounded-full hover:bg-blue-800 m-2" @click="add = !add">
        {{ add ? 'Annuler' : 'Ajouter des cours à une section' }}</button>
    <div v-if="add">
        <form @submit.prevent="submitForm" method="POST">
            <div class="bg-gray-300 flex">
                <select name="section" id="section" v-model="form.section" @change="updateCourses(), form.course = []">
                    <option v-for="section in sections" :value="section.id">{{ section.name }}</option>
                </select>
                <div v-if="errors.section" class="font-bold text-red-500">{{ errors.section }}</div>
                <button type="submit" class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full">
                    Ajouter les cours à la section
                </button>
            </div>
            <div v-if="errors.course" class="font-bold text-red-500">{{ errors.course }}</div>
            <ul class="w-full">
                <li class="flex justify-start items-center px-[30%] border-2" v-for="course in coursList" :key="course.id">

                    <input class="mr-4" type="checkbox" name="course" id="course" :value="course.id"
                        v-model="form.course[course.id]" />

                    {{ course.name }}

                </li>
            </ul>
        </form>
    </div>
    <hr class="border-2 border-black mt-2">
    <div>

    </div>
</template>
