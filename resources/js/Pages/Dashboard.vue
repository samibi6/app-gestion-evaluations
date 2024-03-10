<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { AcademicCapIcon } from '@heroicons/vue/24/solid';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-center font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                    <div class="flex justify-center items-center mb-5">
                        <AcademicCapIcon class="block h-16 w-auto text-blue-600" />
                        <h1 class="ms-4 font-thin text-3xl"><span class="font-black">App</span> Gestion
                            Evaluations
                        </h1>
                    </div>
                    <div class="grid grid-cols-3 gap-5 justify-center items-center">
                        <a class="hover:scale-105 origin-center transition block h-full" :href="route('sections.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Sections</h2>
                                <p class="text-gray-600 text-lg">Ajouter / Modifier / Supprimer <br>une section</p>
                            </div>
                        </a>

                        <a class="hover:scale-105 origin-center transition block h-full" :href="route('courses.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Cours</h2>
                                <p class="text-gray-600 text-lg">Ajouter / Modifier / Supprimer <br>un cours</p>
                            </div>
                        </a>

                        <a class="hover:scale-105 origin-center transition block h-full"
                            :href="route('coursSections.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Cours Sections</h2>
                                <p class="text-gray-600 text-lg">Assigner / Retirer <br>cours de section</p>
                            </div>
                        </a>
                        <a class="hover:scale-105 origin-center transition block h-full" :href="route('students.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Etudiants</h2>
                                <p class="text-gray-600 text-lg">Ajouter / Modifier / Supprimer <br>des étudiants</p>
                            </div>
                        </a>


                        <a class="hover:scale-105 origin-center transition block h-full"
                            :href="route('courseStudents.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Cours Etudiants</h2>
                                <p class="text-gray-600 text-lg">Assigner / Retirer <br>étudiant d'un cours</p>
                            </div>
                        </a>







                        <a class="hover:scale-105 origin-center transition block h-full" :href="route('aptitudes.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">AA et critères</h2>
                                <p class="text-gray-600 text-lg">Ajouter / Modifier / Supprimer <br>Acquis
                                    d'apprentissage /
                                    critères pour un cours</p>
                            </div>
                        </a>

                        <a class="hover:scale-105 origin-center transition block col-span-1 h-full"
                            :href="route('invites.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Invitations</h2>
                                <p class="text-gray-600 text-lg">Générer un jeton pour inviter un nouvel utilisateur</p>
                            </div>
                        </a>

                        <a class="hover:scale-105 origin-center transition block col-span-1 h-full"
                            :href="route('evals.index')">
                            <div
                                class="hover:shadow-lg hover:bg-indigo-100 hover:border-blue-600 transition rounded-md border-2 p-5 bg-gray-200 h-full">
                                <h2 class="text-xl mb-1 font-bold text-blue-600">Evaluations</h2>
                                <p class="text-gray-600 text-lg">Evaluer et Générer fiches (Résultats / ajournement /
                                    refus)
                                    pour un ou plusieurs étudiants par cours</p>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>