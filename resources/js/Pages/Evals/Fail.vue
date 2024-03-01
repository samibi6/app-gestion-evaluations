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
    <AppLayout>
        <div>
            <ul class="flex flex-wrap">
                <li class="bg-zinc-300 w-fit p-5 m-2">{{ section.id + ". " + section.name }}
                </li>
                <li class="bg-zinc-300 w-fit p-5 m-2">{{ course.id + ". " + course.name }}
                </li>
                <li class="bg-zinc-300 w-fit p-5 m-2">{{ student.id + ". " + student.last_name + student.first_name }}
                </li>
            </ul>
            <ul>
                <li><a class="cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                        :href="route('evals.adjournment', { courseId: course.id, studentId: student.id, sectionId: section })">Ajournement</a>
                </li>
                <li><a class="cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                        :href="route('evals.denied', { courseId: course.id, studentId: student.id, sectionId: section })">Refus</a>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>