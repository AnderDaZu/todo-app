<template>
    <div class="flex items-center overflow-auto py-4">
        <div class="bg-white rounded-md shadow-md p-5 w-[480px]">
            <h1 class="text-gray-800 font-bold text-2xl mb-8">Detalle de nota: {{ note.title }}</h1>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Título</label >
                <p>{{ note.title }}</p>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Descripción</label >
                <p>{{ note.description }}</p>
            </div>

            <div class="mb-5 py-2 px-3">
                <p class="my-0 text-sm text-gray-400 mb-1.5">Etiquetas</p>
                <label v-for="tag in note.tags" :key="tag.id" class="text-gray-400 text-sm">
                    <span class="mr-2 text-gray-50 font-semibold text-xs bg-blue-500 px-2 py-1 rounded-full">{{ tag.name }}</span>
                </label>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Fecha de creación</label >
                <p class="text-gray-500">{{ formatDate(note.created_at) }}</p>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Fecha de vencimiento</label >
                <p class="text-gray-500">{{ formatDueDate(note.due_date) }}</p>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Imagen</label >
                <img v-if="note.image_path" :src="note.image_path" alt="" class="rounded-lg">
            </div>

            <div class="flex justify-end mt-5">
                <RouterLink :to="{ name: 'editNote', params: { id: note.id } }" class="py-2 px-3 rounded-2xl text-white font-semibold ml-4 cursor-pointer bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-[2px] transition-all duration-500" title="Editar nota">
                    Editar
                </RouterLink>
            </div>
        </div>

    </div>
</template>

<script>
import { RouterLink } from 'vue-router';

    export default {
        data() {
            return {
                tags: [],
                tags_selected: [],
                note: {
                    title: '',
                    description: '',
                    due_date: null,
                    created_at: null,
                    tags: [],
                    image: null
                },
                messageError: null,
                isDisabled: false,
                showSuccessMessage: false,
                successMessage: '',
            }
        },
        created() {
            this.getTags();
            this.getNote();
        },
        computed: {
            formIsValid() {
                return this.note.title !== '' && this.note.description !== '' && this.tags_selected.length > 0;
            }
        },
        methods: {
            formatDate(dateString) {
                if ( !dateString ) {
                    return '-- -- --';
                }
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric'
                });
            },
            formatDueDate(dateString) {
                if ( !dateString ) {
                    return '-- -- --';
                }
                const date = new Date(dateString + 'T00:00:00');
                return date.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric'
                });
            },
            getTags() {
                this.axios.get('/tags')
                .then(response => {
                    this.tags = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                })
            },
            getNote() {
                this.axios.get('/notes/' + this.$route.params.id)
                .then(response => {
                    this.note = response.data.data;
                    this.tags_selected = this.note.tags.map(tag => tag.name);
                })
                .catch(error => {
                    console.log(error);
                })
            },
            updateNote() {
                const formData = new FormData();
                formData.append('title', this.note.title);
                formData.append('description', this.note.description);

                this.tags_selected.forEach((tag, index) => {
                    formData.append(`tags[]`, tag);
                })

                if (this.note.due_date) {
                    formData.append('due_date', this.note.due_date);
                }

                if (this.note.image) {
                    formData.append('image', this.note.image);
                }

                this.axios.post('/notes/' + this.$route.params.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.note = response.data.data;

                    this.showSuccessMessage = true;
                    this.successMessage = response.data.message;
                    setTimeout(() => {
                        this.showSuccessMessage = false;
                        this.successMessage = '';
                    }, 3000);
                })
                .catch(error => {
                    console.log(error);
                });

            },
            deleteNote() {
                if (confirm(`¿Estás seguro de eliminar esta nota: "${this.note.title}"?`)) {
                    // petición al servidor para eliminar la nota
                    this.axios.delete(`/notes/${this.note.id}`)
                    .then((response) => {
                        // Eliminar la nota del array local
                        this.$router.push({ name: 'indexNote' });
                    })
                    .catch(error => {
                        console.error(error);
                    });
                }
            },
            onFileChange(e) {
                this.note.image = e.target.files[0];
            },
            isChecked(option) {
                return this.tags.includes(option.id)
            }
        }
    }
</script>

<style>

.btn {
    @apply py-2 px-3 rounded-2xl text-white font-semibold;
}

</style>
