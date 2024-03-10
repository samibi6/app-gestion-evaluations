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
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Évaluations
            </h2>
        </template>
        <div class="py-12">
            <ul class="grid grid-cols-3 gap-5 py-8 bg-white max-w-7xl mx-auto sm:px-6 lg:px-8 rounded-lg shadow-lg">
                <li class="bg-zinc-300 p-5 m-2 h-full " v-for="section in sections">{{ section.id + ". " +
                    section.name }}
                    <ul>
                        <li class="ml-5 bg-white m-2 pl-2 flex justify-between items-center rounded-full"
                            v-for="course in courses[section.id]">{{ course.id + ". " + course.name }}<a
                                class="rounded-r-full cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2 ml-2"
                                :href="route('evals.show', { courseId: course.id, sectionId: section.id })">Sélectionner</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
