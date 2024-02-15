<script setup>
import { ref } from "vue";
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
// import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['sections', 'courses', 'coursesBySection']);

const form = useForm({
    section: '',
    course: [],
    year: [],
});

const submitForm = () => {
    form.post(route('sections.store'));
};

const sectionSelect = ref();

// const coursSection = props.coursesBySection[6];
// const coursId = coursSection.map(cours => cours.id);
// console.log(coursId);
console.log(props.coursesBySection[6][0].year);

function isAlreadyInSection(id) {
    const sectionCourses = props.coursesBySection[sectionSelect.value];
    if (sectionSelect.value && sectionCourses) {
        const coursId = sectionCourses.map(cours => cours.id);
        for (const key in coursId) {
            if (coursId[key] === id) {
                return true;
            }
        }
    }
}

function getYear(id) {
    const sectionCourses = props.coursesBySection[sectionSelect.value];
    if (sectionCourses) {
        const course = sectionCourses.find(course => course.id === id);
        if (course) {
            return course.year;
        }
    }
    return ''; // Renvoyer une valeur par défaut si l'année n'est pas trouvée
}

// const form = usePrecognitionForm("post", route("sections.store"), {
//     section: '',
//     course: [],
//     year: [],
// });

// form.setValidationTimeout(300);

// const submit = () => form.submit({
//     preserveScroll: true,
//     onSuccess: () => form.reset(),
// });



</script>

<template>
    <div>
        <form @submit.prevent="submitForm" method="POST">
            <div class="bg-gray-300 flex">
                <select name="section" id="section" v-model="sectionSelect">
                    <option v-for="section in sections" :value="section.id">{{ section.name }}</option>
                </select>
                <button :disabled="processing" type="submit"
                    class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full">
                    Ajouter les cours à la section
                </button>
            </div>
            <ul class="w-full">
                <li class="flex justify-between items-center px-[30%] border-2" v-for="course in courses" :key="course.id">

                    <input class="" type="checkbox" name="course" id="course" :value="course.id"
                        :checked="isAlreadyInSection(course.id)" v-model="form.course">

                    {{ course.name }}

                    <input class="" type="number" name="year" id="year" max="3" :value="getYear(course.id)" />
                </li>
            </ul>
        </form>
    </div>
</template>
