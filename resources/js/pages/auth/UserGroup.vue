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
                    placeholder="Cari Data Group"
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
                            <i class="nav-icon fas fas fa-users"></i> User Group
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
                            title="Tambah Data User Group"
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
                        <span v-if="data.value" class="badge badge-pill badge-success">Aktif</span>
                        <span v-if="!data.value" class="badge badge-pill badge-danger">Non Aktif</span>
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
                            v-if="data.item.status"
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
                            v-if="!data.item.status"
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

        <!-- Unit Modal -->
        <b-modal ref="bv-modal-example" :title="modalTitle" footer-class="p-2">
            <div class="d-block text-center px-3 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <!-- Name -->
                        <b-form-group id="group-group" label="Nama:" label-for="name" class="text-left">
                            <validation-provider mode="passive" name="Nama group" :rules="{required: true, unique: { url: urlCheckNameExist, id: form.id }}" v-slot="{ errors }">
                                <b-form-input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Masukan Nama Group"
                                    autofocus
                                ></b-form-input>
                                <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                            </validation-provider>
                        </b-form-group>

                        <!-- Description -->
                        <b-form-group id="group-description" label="Deskripsi (optional) :" label-for="description" class="text-left">
                            <validation-provider mode="passive" name="Deskripsi" :rules="{required: true}" v-slot="{ errors }">
                                <b-form-textarea
                                    class="form-control form-control-sm"
                                    placeholder="Deskripsikan group disini"
                                    v-model="form.description"
                                    rows="4"
                                    no-resize
                                >
                                </b-form-textarea>
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
        urlCheckNameExist: {
            required: true,
            type: String,
        },
    },
    data() {
        return {
            items: [],
            form: {
                id: null,
                name: null,
                description: null,
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
                { key: 'name', label: 'Nama', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left', sortable: true },
                { key: 'description', label: 'Deskrisi', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center', sortable: true },
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
    methods: {
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
            this.modalTitle = 'Tambah Data User Group'
            this.form.url = this.urlStore
            this.form.name = null
            this.form.description = null
            this.form.method = 'POST'
            this.$refs['bv-modal-example'].show()
        },
        updateForm(item) {
            this.modalTitle = 'Ubah Data User Group'
            this.form.id = item.id
            this.form.url = item.url_edit
            this.form.name = item.name
            this.form.description = item.description
            this.form.method = 'PUT'
            this.$refs['bv-modal-example'].show()
        },
        updateStatusForm(item) {
            let message = 'Apakah Anda yakin akan menon-aktifkan data user group?'
            if (!item.status) {
                message = 'Apakah Anda yakin akan mengaktifkan data user group?'
            }

            this.$confirm(message, 'Perhatian', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Batal',
                type: 'warning'
            }).then(() => {
                axios.put(item.url_status_update, { status: !item.status })
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
                    name: this.form.name,
                    description: this.form.description,
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
            this.$confirm('Apakah Anda yakin ingin menghapus "' + item.name + '" dari user group ?', 'Peringatan', {
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
