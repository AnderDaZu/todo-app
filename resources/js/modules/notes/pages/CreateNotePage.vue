<template>
    <div class="flex items-center overflow-auto py-4">
        <form @submit.prevent="createNote" class="bg-white rounded-md shadow-md p-5 w-[480px]" enctype="multipart/form-data">
            <h1 class="text-gray-800 font-bold text-2xl mb-8">Crear nota</h1>

            <div class="mb-4" v-if="messageError">
                <span class="text-red-600 text-base">{{ messageError }}</span>
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Título*</label >
                <input
                    class="pl-2 w-full outline-none border-none"
                    type="text"
                    placeholder="Título de la nota"
                    required
                    v-model="title"
                />
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl relative">
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1">Descripción*</label >
                <textarea rows="4" class="block pl-2 w-full text-gray-700 rounded-lg outline-none border-none"
                    placeholder="Agregar descripción"
                    v-model="description"
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
                <label class="text-[12px] text-gray-400 absolute -top-3 bg-white px-1"> Fecha de vencimiento</label >
                <input
                    class="pl-2 w-full outline-none border-none text-gray-600"
                    type="date"
                    v-model="due_date" />
            </div>

            <div class="border-2 mb-5 py-2 px-3 rounded-2xl">
                <label class="text-gray-700 hover:cursor-pointer">
                    <input type="file" class="text-base"
                        accept="image/*"
                        @change="onFileChange"/>
                </label>
            </div>

            <button type="submit"
                class="block w-full mt-5 py-2 rounded-2xl text-white font-semibold mb-2"
                :class="{
                    'cursor-not-allowed bg-gray-500 hover:bg-gray-500': isDisabled || !formIsValid,
                    'cursor-pointer bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-500': !isDisabled && formIsValid
                }"
                :disabled="!formIsValid">
                Crear
            </button>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                title: '',
                description: '',
                tags: [],
                tags_selected: [],
                image: null,
                due_date: null,
                messageError: null,
                isDisabled: false
            }
        },
        created() {
            this.getTags();
        },
        computed: {
            formIsValid() {
                return this.title !== '' && this.description !== '' && this.tags_selected.length > 0;
            }
        },
        methods: {
            getTags() {
                this.axios.get('/tags')
                .then(response => {
                    this.tags = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                })
            },
            createNote() {
                this.isDisabled = true;

                const formData = new FormData();
                formData.append('title', this.title);
                formData.append('description', this.description);

                this.tags_selected.forEach((tag, index) => {
                    formData.append(`tags[]`, tag);
                })

                if (this.due_date) {
                    formData.append('due_date', this.due_date);
                }

                if (this.image) {
                    formData.append('image', this.image);
                }

                this.axios.post('/notes', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.$router.push({ name: 'indexNote' });
                    this.isDisabled = false;
                })
                .catch(error => {
                    console.log(error);
                    this.messageError = error.response.data.message;
                    this.isDisabled = false;
                });

                console.log(this.image);

            },
            onFileChange(e) {
                this.image = e.target.files[0];
            },
        }
    }
</script>
