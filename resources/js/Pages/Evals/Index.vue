<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['courses', 'sections', 'AllCourses']);
</script>

<template>

    <AppLayout title="Évaluations">
        <template #header>
            <h1 class="text-center font-bold text-2xl mt-12">
                Évaluer des étudiants dans une UE
            </h1>
            <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez choisir l'UE d'une section et évaluer les
                étudiant qui y sont inscrits, ainsi que générer les PDF des fiches d'évaluations</p>
        </template>
        <div class="w-3/4 mx-auto mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-300 border border-gray-400 shadow-xl rounded-lg p-4" v-for="section in sections"
                    :key="section.id">
                    <h2 class="text-xl font-semibold mb-4">{{ section.id }}. {{ section.name }}</h2>
                    <ul>
                        <li v-if="courses[section.id]" v-for="course in courses[section.id]" :key="course.id" class="">

                            <div class="my-4 text-center ">
                                <a :href="route('evals.show', { courseId: course.id, sectionId: section.id })"
                                    class="text-white font-bold  bg-blue-500 hover:bg-blue-600 transition block w-full px-4 py-2 rounded-lg">{{
                                        course.name }}</a>
                            </div>

                        </li>
                        <li v-else>
                            <p>Cette section ne possède pas encore d'UE</p>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
  