<template>
    <div class="flex items-center overflow-auto py-4">
        <form @submit.prevent="updateNote" class="bg-white rounded-md shadow-md p-5 w-[480px]" enctype="multipart/form-data">
            <h1 class="text-gray-800 font-bold text-2xl mb-8">Editar nota: {{ note.title }}</h1>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Título*</label >
                <input
                    class="pl-2 w-full outline-none border-none"
                    type="text"
                    placeholder="Título de la nota"
                    required
                    v-model="note.title"
                />
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Descripción*</label >
                <textarea rows="4" class="block pl-2 w-full text-gray-700 rounded-lg outline-none border-none"
                    placeholder="Agregar descripción"
                    v-model="note.description"
                    required></textarea>
            </div>

            <div class="mb-5 py-2 px-3">
                <p class="my-0 text-sm text-gray-400 mb-1.5">Etiquetas*</p>
                <label v-for="tag in tags" :key="tag.id" class="text-gray-400 text-sm">
                    <input type="checkbox" :value="tag.name" v-model="tags_selected">
                    <span class="ml-1 mr-4 text-gray-700">{{ tag.name }}</span>
                </label>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1"> Fecha de creación</label >
                <p class="text-gray-500">{{ formatDate(note.created_at) }}</p>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1"> Fecha de vencimiento</label >
                <input
                    class="pl-2 w-full outline-none border-none text-gray-600"
                    type="date"
                    v-model="note.due_date" />
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <img v-if="note.image_path" :src="note.image_path" alt="" class="rounded-lg">
                <label class="text-gray-700 hover:cursor-pointer absolute top-6 left-6 p-2 bg-slate-200 rounded-md bg-opacity-90">
                    <input type="file" class="text-base"
                        accept="image/*"
                        @change="onFileChange"/>
                </label>
            </div>

            <div class="flex justify-end mt-5">
                <button type="button"
                    @click="deleteNote"
                    class="btn cursor-pointer bg-red-600 hover:bg-red-700 hover:-translate-y-[2px] transition-all duration-500">
                    Eliminar
                </button>
                <button type="submit"
                    class="py-2 px-3 rounded-2xl text-white font-semibold ml-4"
                    :class="{
                        'cursor-not-allowed bg-gray-500 hover:bg-gray-500': isDisabled || !formIsValid,
                        'cursor-pointer bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-[2px] transition-all duration-500': !isDisabled && formIsValid
                    }"
                    :disabled="!formIsValid">
                    Actualizar
                </button>
            </div>

            <div v-if="showSuccessMessage" class="bg-blue-500 px-3 py-1.5 rounded-sm mt-4 transition-all">
                <p class="text-white text-sm"><span class="font-bold">Mensaje: </span> {{ successMessage }}</p>
            </div>
        </form>

    </div>
</template>

<script>
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
                    // this.$router.push({ name: 'indexNote' });
                    this.note = response.data.data;

                    this.showSuccessMessage = true;
                    this.successMessage = response.data.message;
                    setTimeout(() => {
                        this.showSuccessMessage = false;
                        this.successMessage = '';
                    }, 3000);

                    console.log(response.data);
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
