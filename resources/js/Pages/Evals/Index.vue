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
        <div>
            <ul class="flex flex-wrap">
                <li class="bg-zinc-300 w-fit p-5 m-2" v-for="section in sections">{{ section.id + ". " + section.name }}
                    <ul>
                        <li class="ml-10 bg-white m-2 pl-2 flex justify-between items-center"
                            v-for="course in courses[section.id]">{{ course.name }}<a
                                class="cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2 ml-2"
                                :href="route('evals.show', { courseId: course.id, sectionId: section.id })">Sélectionner</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
