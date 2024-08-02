<template>
    <div class="container">
        <div class="row">
            <div class="col-12 table-responsive">
                <button type="button" class="btn btn-primary mb-3" @click="showModal" data-toggle="modal" data-target="#createCompanyModal">Create</button>
                <table class="table table-bordered">
                   <thead>
                      <tr>
                        <th v-for="heading in table_heading" :key="heading">{{ heading }}</th>
                      </tr>
                   </thead>
                   <tbody>
                      <tr v-if="articles" v-for="article in articles" :key="article">
                        <td><img width="150" class="img-responsive" :src="`storage/${article.image}`"/></td>
                        <td>{{ article.title }}</td>
                        <td>{{ article.status }}</td>
                        <td>
                            <button class="btn btn-primary m-1" @click="editArticle(article.id)" data-toggle="modal" data-target="#createCompanyModal">Edit</button>
                            <button class="btn btn-danger m-1" @click="deleteArticle(article.id)">Delete</button>
                        </td>
                      </tr>
                      <tr v-else>
                        <td colspan="9" class="text-center">no data availabe</td>
                      </tr>
                   </tbody>
                </table>
            </div>
        </div>
        <form @submit.prevent="createArticle">
            <!-- The Modal -->
            <div class="modal" id="createCompanyModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-between bg-primary text-light">
                        <h4 class="modal-title">Create Company</h4>
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
                                <label for="logo">Logo</label>
                                <input class="form-control" type="file" name="logo" id="logo" @change="onFileChange" accept="image/*" />
                              </div>
                              <div class="form-group">
                                <label for="name">Name</label>
                                <input class="form-control" type="text" name="name" id="name" v-model="formData.name" placeholder="Enter Company Name" />
                              </div>
                              <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" v-model="formData.status">
                                    <option value="" selected>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                              </div>
                           </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">{{ processing ? 'Saving' : 'Save' }}</button>
                    </div>

                    </div>
                </div>
            </div>
            <!-- End modal -->
        </form>
    </div>
</template>
<script>
import axios from '../axios'
export default {
    name:"dashboard",
    data(){
        return {
            user:this.$store.state.auth.user,
            table_heading: ['Image','Title','Link','Date','Content','Status','Writer','Editor','Company'],
            formData: {
                id: '',
                logo: '',
                name: '',
                status: '',
            },
            validationErrors:{},
            processing: false,
            articles: [],
            previewImage: ''
        }
    },
    created() {
        this.getArticles();
    },
    methods: {
        onFileChange(e){
            this.formData.logo = e.target.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(this.formData.logo);
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
                formData.append('logo', this.formData.logo);

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
                    self.previewImage = '/storage/'+data.logo
                    self.formData.name = data.name
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
            await axios.get('/api/articles')
            .then(function (response) {
                self.articles = response.data
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        async deleteArticle(id) {
            let self = this
            await axios.delete(`/api/articles/${id}`)
            .then(function (response) {
                self.getArticles()
            })
            .catch(function (error) {
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
        }
    }
}
</script>