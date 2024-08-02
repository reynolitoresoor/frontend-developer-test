<template>
    <div class="container">
        <div class="row">
            <h1>Articles</h1>
            <div class="col-12 table-responsive">
                <button type="button" class="btn btn-primary mb-3" @click="showModal" data-toggle="modal" data-target="#createArticleModal">Create</button>
                <table class="table table-bordered">
                   <thead>
                      <tr>
                        <th v-for="heading in table_heading" :key="heading">{{ heading }}</th>
                      </tr>
                   </thead>
                   <tbody>
                      <tr v-for="article in articles" :key="article">
                        <td><img width="150" class="img-responsive" :src="`storage/${article.image}`"/></td>
                        <td>{{ article.title }}</td>
                        <td>{{ article.link }}</td>
                        <td>{{ article.date }}</td>
                        <td>{{ article.writer.firstname +' '+ article.writer.firstname }}</td>
                        <td>{{ article.editor.firstname +' '+ article.editor.lastname }}</td>
                        <td>{{ article.status === 'active' ? 'Published' : 'Unpublished'  }}</td>
                        <td>
                            <div v-if="(article.status === 'inactive' && user.type === 'Writer') || user.type === 'Editor'">
                                <button class="btn btn-primary m-1" @click="editArticle(article.id)" data-toggle="modal" data-target="#createArticleModal">Edit</button>
                                <button class="btn btn-danger m-1" id="delete" @click="deleteArticle($event,article.id)">Delete</button>
                            </div>
                        </td>
                      </tr>
                   </tbody>
                </table>
            </div>
        </div>
        
        <!-- The Modal -->
        <div class="modal" id="createArticleModal">
            <div class="modal-dialog">
                <form id="article-form" @submit.prevent="createArticle">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-between bg-primary text-light">
                        <h4 class="modal-title">Create Article</h4>
                        <button type="button" class="btn close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" v-if="Object.keys(validationErrors).length > 0">
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <li v-for="(value, key) in validationErrors" :key="key">{{ value[0] }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3 pt-0 mx-auto">
                                    <img v-if="previewImage" :src="previewImage" width="150" class="img-responsive">
                                </div>
                                <div class="form-group">
                                    <label for="company">Related Company</label>
                                    <select class="form-control" v-model="formData.company">
                                       <option value selected disabled>Select Company</option>
                                       <option v-for="company in companies" :key="company" :value="company.id">{{ company.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input class="form-control" type="file" name="image" id="image" @change="onFileChange" accept="image/*" />
                                </div>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input class="form-control" type="text" name="title" id="title" v-model="formData.title" placeholder="Enter Title" />
                                </div>
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <QuillEditor id="content" name="content" v-model:content="formData.content" @ready="onEditorReady($event)" theme="snow"/>
                                </div>
                                <div class="form-group">
                                    <label for="link">Link</label>
                                    <input class="form-control" type="text" name="link" id="link" @change="onChangeLink" placeholder="Enter Link" />
                                </div>
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input class="form-control" type="date" name="date" id="date" v-model="formData.date" placeholder="Enter Date" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="save">{{ processing ? 'Saving' : 'Save' }}</button>
                        <button v-if="(user.type === 'Editor' && formData.status === 'inactive')" id="publish" type="button" @click="publishArticle" class="btn btn-primary">Publish</button>
                    </div>

                    </div>
                </form>
            </div>
        </div>
        <!-- End modal -->
        
    </div>
</template>

<script>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { isWebUri } from 'valid-url';

export default {
    name:"dashboard",
    components: {
        QuillEditor
    },
    data(){
        return {
            user:this.$store.state.auth.user,
            table_heading: ['Image','Title','Link','Date','Writer Name','Editor Name','Status','Actions'],
            formData: {
                company: '',
                image: '',
                title: '',
                link: '',
                date: '',
                content: '',
                status: ''
            },
            validationErrors:{},
            processing: false,
            articles: [],
            previewImage: '',
            companies: []
        }
    },
    created() {
        this.getArticles();
        this.getCompanies();
    },
    methods: {
        publishArticle() {
            this.formData.status = 'active'
            document.getElementById('save').click();
        },
        onFileChange(e){
            this.formData.image = e.target.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(this.formData.image);
            reader.onload = (e) => {
                this.previewImage = e.target.result;
            }
            e.target.value=''
        },
        async createArticle() {
            try {
                this.processing = true
                const formData = new FormData();
                for (const key in this.formData) {
                formData.append(key, this.formData[key]);
                }
                formData.append('image', this.formData.image);
                if(this.user.type === 'Writer') {
                    formData.append('writer',this.user.id);
                    formData.append('editor',1);
                } else {
                    formData.append('editor',this.user.id);
                    formData.append('writer',2);
                }

                // let content = JSON.stringify(this.formData.content.ops);
                // let data = JSON.parse(content);
                //formData.append('content', data[0].insert);
                formData.append('content', this.formData.content.ops);

                if(this.formData.id) {
                    const response = await axios.post(`/api/articles/${this.formData.id}`, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });

                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getArticles();
                    } else {
                        console.error('Unexpected response status:', response.status);
                    }
                    
                } else {
                    formData.append('status', 'inactive')
                    const response = await axios.post('/api/articles', formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });

                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getArticles();
                    } else {
                        console.error('Unexpected response status:', response.status);
                    }
                }
                
            } catch (error) {
                if (error.isAxiosError && error.response) {
                   this.validationErrors = error.response.data.errors;
                } else {
                console.error('An error occurred:', error);
                }
            }
        },
        async editArticle(id) {
            try {
                let self = this
                await axios.get(`/api/articles/${id}`)
                .then(function (response) {
                    var data = response.data
                    self.formData.id = data.id
                    self.previewImage = '/storage/'+data.image
                    self.formData.title = data.title
                    self.formData.link = data.link
                    document.getElementById('link').value = data.link
                    self.formData.date = data.date
                    self.formData.company = data.company
                    self.formData.content = data.content
                    self.formData.status = data.status
                })
                .catch(function (error) {
                    console.log(error);
                })
            } catch (error) {
                if (error.isAxiosError && error.response) {
                   this.validationErrors = error.response.data.errors;
                } else {
                console.error('An error occurred:', error);
                }
            }
        },
        async getArticles() {
            let self = this
            if(self.user.type === 'Editor') {
                var api = '/api/articles'
            } else {
                var api = '/api/articles/user/'+self.user.id
            }
            await axios.get(api)
            .then(function (response) {
                self.articles = response.data
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        async getCompanies() {
            let self = this
            await axios.get('/api/companies')
            .then(function (response) {
                self.companies = response.data
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        async deleteArticle(e, id) {
            let self = this
            e.target.innerHTML = 'Deleting...';
            await axios.delete(`/api/articles/${id}`)
            .then(function (response) {
                if(response.status) {
                    self.getArticles()
                }
            })
            .catch(function (error) {
                alert('Unable to delete article!')
                console.log(error);
            })
        },
        showModal() { 
            this.resetForm();
        },
        resetForm() {
            this.formData.logo = '';
            this.formData.name = '';
            this.formData.status = '';
        },
        onChangeLink(e) {
            let self = this
            let link = e.target.value
            if (!isWebUri(link)) {
                alert("Not a valid url.");
                return false;
            }
            self.formData.link = link
        },
        onEditorReady (e) {
            let self = this
            e.container.querySelector('.ql-blank').innerHTML = self.formData.content
        }
    }
}
</script>