<template>
    <div>
        <div class="search-and-filter row mb-2 d-flex align-items-center">
            <!-- Perpage -->
            <div class="col-12 col-md-6">
                <b-form-select
                    v-model="queryParams.per_page"
                    :options="pageOptions"
                    size="sm"
                    class="d-inline select-per-page"
                ></b-form-select>
                <span class="text-white">Data Per Halaman</span>
            </div>

            <!-- Input search -->
            <div class="col-12 col-md-6 text-md-right">
                <input
                    type="text"
                    name="search"
                    v-model="queryParams.keyword"
                    class="form-control form-control-sm search-form"
                    placeholder="Cari Data User"
                    @change="search()"
                >
            </div>
        </div>

        <div class="card">
            <!-- Card header -->
            <div class="card-header" style="background-color: rgb(232, 232, 232) !important;">
                <div class="row d-flex align-items-center">
                    <!-- Title Bar -->
                    <div class="col-12 col-md-8">
                        <h3 class="card-title">
                            <i class="nav-icon fas fas fa-user"></i> User
                        </h3>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-12 col-md-4 text-left text-md-right">
                        <!-- Export Button -->
                        <button
                            type="button"
                            class="btn btn-default dropdown-toggle dropdown-icon"
                            data-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="fas fa-file-export"></i> Export
                            <div class="dropdown-menu" role="menu" x-placement="bottom-start">
                                <a class="dropdown-item" href="#">Excel</a>
                                <a class="dropdown-item" href="#">Word</a>
                                <a class="dropdown-item" href="#">PDF</a>
                            </div>
                        </button>

                        <!-- Add Button -->
                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Tambah Data User"
                            @click.prevent="addForm()"
                        >
                            <i class="fas fa-plus-square"></i> Tambah
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body py-0">
                <b-table
                    :items="fetchData"
                    :fields="fields"
                    ref="table"
                    :per-page="queryParams.per_page"
                    :current-page="queryParams.page"
                    :busy.sync="isBusy"
                    :sort-by.sync="queryParams.order_by"
                    :sort-desc.sync="queryParams.sort_desc"
                    table-class="custom-table"
                    show-empty
                    hover
                    striped
                    responsive
                >
                    <!-- Index -->
                    <template v-slot:cell(index)="data">
                        {{ (queryParams.page !== 1) ? (data.index + 1) + (queryParams.per_page) * (queryParams.page - 1) : data.index + 1 }}
                    </template>

                    <!-- Status -->
                    <template v-slot:cell(status)="data">
                        <span v-if="data.value==1" class="badge badge-pill badge-success">Aktif</span>
                        <span v-if="data.value==2" class="badge badge-pill badge-danger">Banned</span>
                    </template>

                    <!-- Action -->
                    <template v-slot:cell(action)="data">
                        <!-- Edit Button -->
                        <a
                            href="#"
                            @click.prevent="updateForm(data.item)"
                            class="action-button text-secondary"
                            data-toggle="tooltip"
                            title="Ubah"
                        >
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Enable Button -->
                        <a
                            href="#"
                            class="action-button text-secondary"
                            data-toggle="tooltip"
                            title="Non aktifkan"
                            v-if="data.item.status==1"
                            @click.prevent="updateStatusForm(data.item)"
                        >
                            <i class="fas fa-toggle-on"></i>
                        </a>

                        <!-- Disable Button -->
                        <a
                            href="#"
                            class="action-button text-secondary"
                            data-toggle="tooltip"
                            title="Aktifkan"
                            v-if="data.item.status==2"
                            @click.prevent="updateStatusForm(data.item)"
                        >
                            <i class="fas fa-toggle-off"></i>
                        </a>

                        <a
                            href="#"
                            class="action-button text-secondary"
                            data-toggle="tooltip"
                            title="Hapus"
                            @click.prevent="destroy(data.item)"
                        >
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </template>

                    <!-- Empty Message -->
                    <template v-slot:empty="scope">
                        <h4 class="m-0 p-2 text-secondary">Tidak ada data untuk ditampilkan.</h4>
                    </template>
                </b-table>
            </div>

            <!-- Footer -->
            <div class="card-footer">
                <div class="row">
                    <!-- Text record -->
                    <div class="col-12 col-md-6 text-muted">
                        <span>Menampilkan {{ meta.from }} sampai {{ meta.to }} dari total {{ meta.total }} data.</span>
                    </div>

                    <!-- Pagination -->
                    <div class="col-12 col-md-6">
                        <b-pagination
                            v-model="queryParams.page"
                            :total-rows="totalRows"
                            :per-page="queryParams.per_page"
                            align="right"
                            size="sm"
                            class="mb-0"
                        ></b-pagination>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <b-modal ref="bv-modal-example" :title="modalTitle" footer-class="p-2">
            <div class="d-block text-center px-3 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <!-- Employee -->
                        <b-form-group id="group-employee-nik" label="NIK :" label-for="employee_nik" class="text-left">
                            <validation-provider mode="passive" name="NIK" :rules="{required: true}" v-slot="{ errors }">
                                <v-select
                                    id="employee_nik"
                                    placeholder="Ketik NIK/Nama Karyawan"
                                    label="identity"
                                    :options="employeeOptions"
                                    :filterable="false"
                                    v-model="form.employee_nik"
                                    @search="findEmployees"
                                >
                                    <template slot="no-options">
                                        Cari Karyawan..
                                    </template>

                                        <template v-slot:option="option">
                                        {{ option.identity }}
                                        </template>

                                        <template slot="selected-option" slot-scope="option">
                                        {{ option.identity }}
                                    </template>

                                </v-select>
                                <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                            </validation-provider>
                        </b-form-group>

                        <!-- Level Group -->
                        <b-form-group id="group-level-group" label="Level Group :" label-for="user_group_id" class="text-left">
                            <validation-provider mode="passive" name="Level group" :rules="{required: true}" v-slot="{ errors }">
                                <v-select
                                    label="name"
                                    v-model="form.user_group_id"
                                    :options="levelGroupOptions"
                                    :reduce="name => name.id"
                                >
                                </v-select>
                                <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                            </validation-provider>
                        </b-form-group>

                        <!-- Username -->
                        <b-form-group id="group-group" label="Username :" label-for="username" class="text-left">
                            <validation-provider mode="passive" name="Username" :rules="{required: true, unique: { url: urlCheckUsernameExist, id: form.id }}" v-slot="{ errors }">
                                <b-form-input
                                    id="username"
                                    v-model="form.username"
                                    type="text"
                                    required
                                    placeholder="Masukan username"
                                    autofocus
                                    autocomplete="false"
                                ></b-form-input>
                                <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                            </validation-provider>
                        </b-form-group>

                        <!-- Password -->
                        <b-form-group id="group-group" label="Password :" label-for="password" class="text-left">
                            <validation-provider mode="passive" name="password" :rules="{required: password_required }" v-slot="{ errors }">
                                <b-form-input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    placeholder="Masukan password"
                                    autofocus
                                ></b-form-input>
                                <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                            </validation-provider>
                        </b-form-group>

                        <button type="submit" class="d-none">Kirim</button>
                    </b-form>
                </validation-observer>
            </div>

            <template v-slot:modal-footer>
                <div class="w-100 m-0 text-center">
                    <button class="btn btn-sm btn-primary" @click="submitForm()">
                        Kirim
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0" @click="hideModal()">
                        Batal
                    </button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import vSelect from 'vue-select'
import { findEmployees } from '../../api/employee'
import { extend } from 'vee-validate'
import { Notification, MessageBox } from 'element-ui'
import { required } from 'vee-validate/dist/rules';
import { BModal, BTable, BPagination, BFormSelect, BForm, BFormGroup, BFormInput, BFormCheckbox, BFormTextarea } from 'bootstrap-vue'

// Validation
extend('required', required);

extend('unique', {
    params: ['url', 'id'],
    async validate(value, { url, id }) {
        let status

        await axios.get(url, { params: { value: value, id: id } })
                .then(response => {
                    status = response.data.status
                })
                .catch(error => {
                    status = error.response.data.status
                })

        return status
    },
    message: '{_field_} sudah terdaftar'
});

export default {
    components: {
        BModal,
        BTable,
        BPagination,
        BFormSelect,
        BForm,
        BFormGroup,
        BFormInput,
        BFormCheckbox,
        BFormTextarea,
        Notification,
        MessageBox,
        vSelect,
    },
    props: {
        urlData: {
            required: true,
            type: String,
        },
        urlStore: {
            required: true,
            type: String,
        },
        urlCheckUsernameExist: {
            required: true,
            type: String,
        },
        urlCheckEmailExist: {
            required: true,
            type: String,
        },
        urlLevelGroupOptions: {
            required: true,
            type: String,
        },
    },
    data() {
        return {
            items: [],
            employeeOptions: [],
            levelGroupOptions: [],
            password_required:true,
            form: {
                id: null,
                employee_nik: null,
                user_group_id: null,
                username: null,
                password: null,
                url: null,
                method: 'POST'
            },
            queryParams: {
                per_page: 15,
                keyword: null,
                page: 1,
                sort_desc: false,
                order_by: null,
            },
            totalRows: 0,
            isBusy: false,
            fields: [
                { key: 'index', label: '#', thStyle: 'text-align: center; width: 35px;', tdClass: 'custom-cell text-center' },
                { key: 'employee_nik', label: 'NIK', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'full_name', label: 'Nama', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'departement_name', label: 'Departemen', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'username', label: 'Username', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'group', label: 'Level', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'status', label: 'Status', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center', sortable: true },
                { key: 'action', label: 'Aksi', thStyle: 'text-align: center; min-width: 60px;', tdClass: 'custom-cell text-center' }
            ],
            meta: [],
            modalTitle: undefined,
            pageOptions: [
                { text: 15, value: 15, disabled: false },
                { text: 25, value: 25, disabled: false },
                { text: 50, value: 50, disabled: false },
                { text: 100, value: 100, disabled: false },
            ],
        }
    },
    mounted(){
        this.loadLevelGroupOptions();
    },
    methods: {
        loadLevelGroupOptions(){
            axios.get(this.urlLevelGroupOptions)
            .then(response => {
                this.levelGroupOptions = response.data.data
                this.levelGroupOptions.unshift({ id: null, name: 'Pilih Level Group' })
            })
            .catch(error => console.log(error));
        },
        findEmployees(keyword, loading) {
            this.loading = true
            this.queryParams.keyword = keyword
            findEmployees(this.queryParams).then(response => {
                this.employeeOptions = response.data.data
                this.loading = false
            })
        },
        fetchData(ctx) {
            let promise = axios.get(this.urlData, { params: this.queryParams })

            return promise.then((response) => {
                    this.items = response.data
                    const items = response.data.data
                    this.meta = response.data.meta
                    this.totalRows = response.data.meta.total

                    return (items)
                })
                .catch((error) => {
                    return []
                })
        },
        addForm() {
            this.modalTitle = 'Tambah Data User'
            this.form.url = this.urlStore
            this.form.employee_nik = null
            this.form.user_group_id = null
            this.form.username = null
            this.form.password = null
            this.form.method = 'POST'
            this.$refs['bv-modal-example'].show()
        },
        updateForm(item) {
            this.modalTitle = 'Ubah Data User'
            this.form.id = item.id
            this.form.url = item.url_edit
            this.form.employee_nik = item.employee
            this.form.user_group_id = item.user_group_id
            this.form.username = item.username
            this.form.password = null
            this.password_required = false
            this.form.method = 'PUT'
            this.$refs['bv-modal-example'].show()

        },
        updateStatusForm(item) {
            var message = null
            var status = 1

            if (item.status==2) {
                status = 1
                message = 'Apakah Anda yakin akan mengaktifkan user '+ item.username +' ?'
            }else{
                status = 2
                message = 'Apakah Anda yakin akan mem-banned user '+ item.username +' ?'
            }

            this.$confirm(message, 'Perhatian', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Batal',
                type: 'warning'
            }).then(() => {
                axios.put(item.url_status_update, { 'status': status , 'username': item.username })
                    .then(response => {
                        this.$refs.table.refresh()
                        this.$message({
                            type: 'success',
                            message: response.data.message
                        });
                    })
            })
        },
        async submitForm() {
            const isValid = await this.$refs.observer.validate();
            if (!isValid) {
                return
            }

            axios({
                method: this.form.method,
                url: this.form.url,
                data: {
                    employee_nik: this.form.employee_nik.nik,
                    user_group_id: this.form.user_group_id,
                    username: this.form.username,
                    password: this.form.password,
                }
            })
                .then(response => {
                    this.hideModal()
                    this.queryParams.keyword = this.form.name
                    this.$notify({
                        title: 'Sukses',
                        message: response.data.message,
                        type: 'success',
                    })
                    this.$refs.table.refresh()
                })
                .catch(error => {
                    const errorResponse = error.response

                    if (errorResponse.status !== 422) {
                        this.hideModal()
                        this.$message({
                            showClose: true,
                            message: 'Oops, Sepertinya error nih, mohon dicoba kembali ya!',
                            type: 'error'
                        });
                    }
                })
        },
        destroy(item) {
            this.$confirm('Apakah Anda yakin ingin menghapus user "' + item.username + '"?', 'Peringatan', {
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                type: 'error'
            })
                .then(() => {
                    axios.delete(item.url_delete)
                        .then(response => {
                            this.$refs.table.refresh();
                            this.$message({
                                type: 'success',
                                message: response.data.message
                            });
                        })
                        .catch(error => {
                            this.$message({
                                type: 'error',
                                message: error.response.status + ': ' +error.response.data.message
                            });
                        })
                })
                .catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Penghapusan dibatalkan.'
                    })
                })
        },
        hideModal() {
            this.$refs['bv-modal-example'].hide()
        },
        search() {
            this.fetchData()
            this.$refs.table.refresh()
        },
    }
}
</script>
