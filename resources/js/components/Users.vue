<template>
    <div class="container">
        <div class="row">
            <div class="col-12 table-responsive">
                <button type="button" class="btn btn-primary mb-3" @click="showModal" data-toggle="modal" data-target="#createUserModal">Create</button>
                <table class="table table-bordered">
                   <thead>
                      <tr>
                        <th v-for="heading in table_heading" :key="heading">{{ heading }}</th>
                      </tr>
                   </thead>
                   <tbody>
                      <tr v-if="users" v-for="user in users" :key="user">
                        <td>{{ user.email }}</td>
                        <td>{{ user.firstname }}</td>
                        <td>{{ user.lastname }}</td>
                        <td>{{ user.type }}</td>
                        <td>{{ user.status }}</td>
                        <td>
                            <button class="btn btn-primary m-1" @click="editUser(user.id)" data-toggle="modal" data-target="#createUserModal">Edit</button>
                            <button class="btn btn-danger m-1" id="delete" @click="deleteUser($event,user.id)">Delete</button>
                        </td>
                      </tr>
                      <tr v-else>
                        <td colspan="9" class="text-center">no data availabe</td>
                      </tr>
                   </tbody>
                </table>
            </div>
        </div>
        <form @submit.prevent="createUser">
            <!-- The Modal -->
            <div class="modal" id="createUserModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-between bg-primary text-light">
                        <h4 class="modal-title">Create User</h4>
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
                              <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input class="form-control" type="text" name="firstname" v-model="formData.firstname" id="firstname" placeholder="Enter First Name" />
                              </div>
                              <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input class="form-control" type="text" name="lastname" id="lastname" v-model="formData.lastname" placeholder="Enter Last Name" />
                              </div>
                              <div class="form-group">
                                <label for="email">Email</label>
                                <input class="form-control" type="text" name="email" id="email" v-model="formData.email" placeholder="Enter Email" />
                              </div>
                              <div class="form-group">
                                <label for="password">Password</label>
                                <input class="form-control" type="password" name="password" id="password" v-model="formData.password" placeholder="Enter Password" />
                              </div>
                              <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input class="form-control" type="password" name="confirm_password" id="confirm_password" v-model="formData.confirm_password" placeholder="Enter Confirm Password" />
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
    name:"users",
    data(){
        return {
            user:this.$store.state.auth.user,
            table_heading: ['Email','First Name','Last Name','Type','Status','Actions'],
            formData: {
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            users: [],
            validationErrors:{},
            processing: false,
        }
    },
    created() {
        this.getUsers();
    },
    methods: {
        async createUser() {
            try {
                this.processing = true
                const formData = new FormData();
                for (const key in this.formData) {
                formData.append(key, this.formData[key]);
                }

                if(this.formData.password !== this.formData.confirm_password) {
                    alert('Password is not match!')
                    return false;
                }

                if(this.formData.id) {
                    const response = await axios.post(`/api/users/${this.formData.id}`, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });
                    console.log(response);
                    
                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getUsers();
                    } else {
                        console.error('Unexpected response status:', response.status);
                    }
                    
                } else {
                    const response = await axios.post('/api/users', formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                    });

                    if (response.status) {
                        document.getElementById('close').click();
                        this.processing = false;
                        this.getUsers();
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
        async editUser(id) {
            try {
                let self = this
                await axios.get(`/api/users/${id}`)
                .then(function (response) {
                    var data = response.data
                    self.formData.id = data.id
                    self.formData.firstname = data.firstname
                    self.formData.lastname = data.lastname
                    self.formData.email = data.email
                    self.formData.password = '';
                    self.formData.confirm_password = ''
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
        async getUsers() {
            let self = this
            await axios.get('/api/users')
            .then(function (response) {
                self.users = response.data
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        async deleteUser(e, id) {
            let self = this
            e.target.innerHTML = 'Deleting...'
            await axios.delete(`/api/users/${id}`)
            .then(function (response) {
                if(response.status) {
                    self.getUsers()
                }
            })
            .catch(function (error) {
                alert('Unable to delete user!')
                console.log(error);
            })
        },
        showModal() { 
            this.resetForm();
        },
        resetForm() {
            this.formData.firstname = '';
            this.formData.lastname = '';
            this.formData.email = '';
            this.formData.password = '';
            this.formData.confirm_password = '';
        }
    }
}
</script>