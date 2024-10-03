<template>
    <div class="">
        <div class="flex justify-between items-center my-5">
            <h2 class="mr-6">Hola {{ auth.user.name }}, acá podras gestionar las notas</h2>
            <RouterLink :to="{ name: 'createNote' }" title="Agregar nota" class="py-1.5 px-3 transition-colors bg-blue-500 hover:bg-blue-600 text-gray-100 hover:text-gray-50 rounded-full"><i class="fa-solid fa-plus"></i></RouterLink>
        </div>

        <div class="" v-if="notes.length > 0">
            <div class="flex gap-2 mb-4 items-center">
                <p class="font-semibold">Ordenar por:</p>
                <select v-model="sort" class="px-2 py-1.5 outline-1 outline-blue-500 rounded-md bg-gray-50">
                    <option value="-created_at">Fecha creación (recientes)</option>
                    <option value="created_at">Fecha creación (antiguos)</option>
                    <option value="-due_date">Fecha vencimiento (lejanas)</option>
                    <option value="due_date">Fecha vencimiento (proximas)</option>
                </select>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                    <thead class="text-xsuppercase bg-gray-700 text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Título
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Fecha creación
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Fecha vencimiento
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="note in notes" :key="note.id" class="bg-gray-800 border-gray-700 hover:bg-gray-600 hover:text-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium whitespace-nowrap text-white">
                                {{ note.id }}
                            </th>
                            <td class="px-6 py-4">
                                {{ note.title }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatDate(note.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formDueDate(note.due_date) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <RouterLink :to="{ name: 'showNote', params: { id: note.id } }" class="text-slate-400" title="Ver nota"><i class="fa-regular fa-eye"></i></RouterLink>
                                <RouterLink :to="{ name: 'editNote', params: { id: note.id } }" class="text-blue-500 ml-4" title="Editar nota"><i class="fa-solid fa-pen-to-square"></i></RouterLink>
                                <button @click="deleteNote(note.id, note.title)" class="ml-4 text-red-500" title="Eliminar nota"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else class="bg-gray-300 py-4 px-3 rounded-md">
            <p class="text-gray-700 text-base italic"><span class="font-semibold not-italic">Mensaje:</span> no hay notas creadas</p>
        </div>

        <div v-if="showSuccessMessage" class="bg-blue-600 px-3 py-1.5 rounded-sm transition-transform mt-4">
            <p class="text-white text-sm"><span class="font-bold">Mensaje: </span> {{ successMessage }}</p>
        </div>
    </div>
</template>

<script>
import { RouterLink } from 'vue-router';
import { mapState } from 'vuex';

export default {
    data() {
        return {
            notes: [],
            showSuccessMessage: false,
            successMessage: '',
            sort: '-created_at',
        }
    },
    created() {
        this.getNotes();
    },
    computed: {
        ...mapState([
            'auth'
        ])
    },
    watch: {
        sort(newValue, oldValue) {
            if ( newValue != oldValue ) {
                this.getNotes();
            }
        }
    },
    methods: {
        getNotes() {
            this.axios.get('/notes', { params: { sort: this.sort } }
            ).then(response => {
                this.notes = response.data.data;
            }).catch(error => {
                console.log(error.response.data);
            });
        },
        formatDate(dateString) {
            if ( !dateString ) {
                return '-- -- --';
            }
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        formDueDate(dateString) {
            if ( !dateString ) {
                return '-- -- --';
            }
            const date = new Date(dateString + 'T00:00:00');
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        deleteNote(id, title) {
            if (confirm(`¿Estás seguro de eliminar esta nota: "${title}"?`)) {
                this.axios.delete(`/notes/${id}`)
                .then((response) => {
                    this.showSuccessMessage = true;
                    this.successMessage = response.data.message;
                    setTimeout(() => {
                        this.showSuccessMessage = false;
                        this.successMessage = '';
                    }, 3000);

                    // Eliminar la nota del array local
                    this.notes = this.notes.filter(note => note.id !== id);

                })
                .catch(error => {
                    console.error(error);
                });
            }
        }
    }
}
</script>
