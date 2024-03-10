<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'section', 'student']);
</script>

<template>
    <AppLayout title="Évaluations">
        <div class="p-5 shadow-md mt-12">
            <h2 class="text-2xl font-bold text-center">Échec de</h2>
            <h1 class="my-5 mb-20 font-bold text-3xl underline text-center">{{ student.first_name + " " + student.last_name
            }} -
                {{
                    course.name }} - {{ section.name }}
            </h1>
            <div class="border border-gray-400 bg-gray-300 mx-auto w-1/3 p-10 rounded-md shadow-xl">
                <a class="cursor-pointer mb-20 font-bold text-red-500 hover:bg-red-500 hover:text-white transition inline-block px-4 py-2 mb-5 border border-red-500 rounded-md"
                    :href="route('evals.edit', { courseId: course.id, studentId: student.id, sectionId: section })">Retour</a>
                <h2 class="font-semibold text-center text-lg">Veuillez choisir un type d'échec pour l'étudiant :</h2>
                <div class="flex justify-center mt-5 ">
                    <a class="font-bold text-white bg-orange-600 hover:bg-orange-500 inline-block px-4 py-2 m-2 rounded-md"
                        :href="route('evals.adjournment', { courseId: course.id, studentId: student.id, sectionId: section })">Ajournement</a>
                    <a class="font-bold text-white bg-red-500 hover:bg-red-600 inline-block px-4 py-2 m-2 rounded-md"
                        :href="route('evals.denied', { courseId: course.id, studentId: student.id, sectionId: section })">Refus</a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>