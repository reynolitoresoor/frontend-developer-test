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
                      <tr v-for="company in companies" :key="company">
                        <td><img width="150" class="img-responsive" :src="`storage/${company.logo}`"/></td>
                        <td>{{ company.name }}</td>
                        <td>{{ company.status }}</td>
                        <td>
                            <button class="btn btn-primary m-1" @click="editCompany(company.id)" data-toggle="modal" data-target="#createCompanyModal">Edit</button>
                            <button class="btn btn-danger m-1" id="delete" @click="deleteCompany($event,company.id)">Delete</button>
                        </td>
                      </tr>
                   </tbody>
                </table>
            </div>
        </div>
        
        <!-- The Modal -->
        <div class="modal" id="createCompanyModal">
            <div class="modal-dialog">
                <form @submit.prevent="createCompany">
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
                </form>
            </div>
        </div>
        <!-- End modal -->
    </div>
</template>
<script>
import axios from '../axios'
export default {
    name:"company",
    data(){
        return {
            user:this.$store.state.auth.user,
            table_heading: ['Logo','Name','Status','Actions'],
            formData: {
                id: '',
                logo: '',
                name: '',
                status: '',
            },
            validationErrors:{},
            processing: false,
            companies: [],
            previewImage: ''
        }
    },
    created() {
        this.getCompany();
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
        async createCompany() {
            try {
                this.processing = true
                const formData = new FormData();
                for (const key in this.formData) {
                formData.append(key, this.formData[key]);
                }
                formData.append('logo', this.formData.logo);

                if(this.formData.id) {
                    const response = await axios.post(`/api/companies/${this.formData.id}`, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });

                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getCompany();
                    } else {
                        console.error('Unexpected response status:', response.status);
                    }
                    
                } else {
                    const response = await axios.post('/api/companies', formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });

                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getCompany();
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
        async editCompany(id) {
            try {
                let self = this
                await axios.get(`/api/companies/${id}`)
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
        async getCompany() {
            let self = this
            await axios.get('/api/companies')
            .then(function (response) {
                self.companies = response.data
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        async deleteCompany(e, id) {
            let self = this
            e.target.innerHTML = 'Deleting...'
            await axios.delete(`/api/companies/${id}`)
            .then(function (response) {
                self.getCompany()
            })
            .catch(function (error) {
                alert('Unable to delte company!')
                console.log(error);
            })
        },
        showModal() { 
            this.resetForm();
        },
        resetForm() {
            this.previewImage = '';
            this.formData.logo = '';
            this.formData.name = '';
            this.formData.status = '';
        }
    }
}
</script>