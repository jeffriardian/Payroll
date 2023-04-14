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
                    placeholder="Cari Data Kandidat"
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
                            <i class="nav-icon fas fa-layer-group"></i> Data Pesangon
                        </h3>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-12 col-md-8 ">
                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Upload Absensi Staff"
                            @click.prevent="UploadPesangon()"
                        >
                            <i class="fas fa-upload"></i> Upload Pesangon
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
                    table-class="custom-table"
                    hover
                    striped
                    responsive
                >
                    <!-- Index -->
                    <template v-slot:cell(index)="data">
                        {{ (queryParams.page !== 1) ? (data.index + 1) + (queryParams.per_page) * (queryParams.page - 1) : data.index + 1 }}
                    </template>

                    <template v-slot:cell(date_created)="data">
                        {{ data.value | moment_date }}
                    </template>

                    <template v-slot:cell(periode_awal)="data">
                        {{ data.value | moment_date }}
                    </template>

                    <template v-slot:cell(periode_akhir)="data">
                        {{ data.value | moment_date }}
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

        <!-- Proses Payroll Modal -->
        <b-modal ref="bv-modal-example" :title="modalTitle" footer-class="p-2" size="m">
            <div class="d-block text-center px-3 py-2">
                <!-- <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()"> -->
                <validation-observer ref="observer" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <div class="row bg-light">
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Tanggal Buat -->
                                <b-form-group id="group-date_created" label="Tanggal Buat :" label-for="date_created" class="text-left">
                                    <validation-provider mode="passive" name="Tanggal Buat" :rules="{required: true}" v-slot="{ errors }">
                                        <datepicker
                                            id="date_created"
                                            v-model="form.date_created"
                                            type="date"
                                            lang="en"
                                            format="DD MMMM YYYY"
                                            placeholder="Masukan Tanggal Buat"
                                            input-class="form-control form-control-sm bg-white"
                                            style="background-color:#FF0"
                                        ></datepicker>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Company -->
                                <b-form-group id="group-company" label="Perusahaan :" label-for="company" class="text-left">
                                    <validation-provider mode="passive" name="Perusahaan" :rules="{required: true}" v-slot="{ errors }">
                                        <v-select
                                            id="company"
                                            v-model="form.company"
                                            :options = "companies"
                                            :reduce = "name => name.id"
                                            label="name"
                                            required
                                        >
                                        </v-select>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>
                        <div class="row bg-light">
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Periode Awal -->
                                <b-form-group id="group-periode_awal" label="Periode Awal :" label-for="periode_awal" class="text-left">
                                    <validation-provider mode="passive" name="Periode Awal" :rules="{required: true}" v-slot="{ errors }">
                                        <datepicker
                                            id="periode_awal"
                                            v-model="form.periode_awal"
                                            type="date"
                                            lang="en"
                                            format="DD MMMM YYYY"
                                            placeholder="Masukan Periode Awal"
                                            input-class="form-control form-control-sm bg-white"
                                            style="background-color:#FF0"
                                        ></datepicker>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Periode Akhir -->
                                <b-form-group id="group-periode_akhir" label="Periode Akhir :" label-for="periode_akhir" class="text-left">
                                    <validation-provider mode="passive" name="Periode Akhir" :rules="{required: true}" v-slot="{ errors }">
                                        <datepicker
                                            id="periode_akhir"
                                            v-model="form.periode_akhir"
                                            type="date"
                                            lang="en"
                                            format="DD MMMM YYYY"
                                            placeholder="Masukan Periode Akhir"
                                            input-class="form-control form-control-sm bg-white"
                                            style="background-color:#FF0"
                                        ></datepicker>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>
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

        <!-- Upload Payroll Modal -->
        <b-modal ref="bv-modal-upload" :title="modalTitle" footer-class="p-2" size="m">
            <!-- Card Body -->
            <div class="card-body py-0">
                <div class="d-block text-center px-3 py-2">
                    <!-- <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitFormUpload()"> -->
                    <validation-observer ref="observer" tag="form" @submit.prevent="submitFormUpload()">
                        <b-form @submit.prevent="submitFormUpload" enctype="multipart/form-data">
                            <div class="row bg-light">
                                <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                    <!-- Bulan -->
                                    <b-form-group id="group-bulan" label="Bulan :" label-for="bulan" class="text-left">
                                        <validation-provider mode="passive" name="Bulan" :rules="{required: true}" v-slot="{ errors }">
                                            <v-select
                                                id="bulan"
                                                v-model="form.bulan"
                                                :options = "bulans"
                                                :reduce = "text => text.value"
                                                label="text"
                                                required
                                            >
                                            </v-select>
                                            <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                        </validation-provider>
                                    </b-form-group>
                                </div>
                                <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                    <!-- Tahun -->
                                    <b-form-group id="group-tahun" label="Tahun :" label-for="tahun" class="text-left">
                                        <validation-provider mode="passive" name="Tahun" :rules="{required: true}" v-slot="{ errors }">
                                            <v-select
                                                id="tahun"
                                                v-model="form.tahun"
                                                :options = "tahuns"
                                                :reduce = "text => text.value"
                                                label="text"
                                                required
                                            >
                                            </v-select>
                                            <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                        </validation-provider>
                                    </b-form-group>
                                </div>
                            </div>
                            <div class="row bg-light">
                                <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                   <label for="">Pilih File (.xls, .xlsx)</label>
                                    <input type="file" class="form-control" @change="uploadExcell($event)">
                                    <!-- <p class="text-danger">{{ $errors->first('file') }}</p> -->
                                </div>
                            </div>
                        </b-form>
                    </validation-observer>
                </div>
            </div>

            <template v-slot:modal-footer>
                <div class="w-100 m-0 text-center">
                    <button class="btn btn-sm btn-primary" @click="submitFormUpload()">
                        Kirim
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0" @click="hideModalUpload()">
                        Batal
                    </button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import vSelect from 'vue-select'
import Datepicker from 'vue2-datepicker'
import moment from 'moment'
import 'vue2-datepicker/index.css';
import { extend } from 'vee-validate'
import { Notification, MessageBox } from 'element-ui'
import { required } from 'vee-validate/dist/rules';
import { BModal, BTable, BPagination, BFormSelect, BForm, BFormGroup, BFormInput, BFormCheckbox, BFormRadioGroup } from 'bootstrap-vue'

// Validation
extend('required', required);

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
        Notification,
        MessageBox,
        vSelect,
        Datepicker,
        BFormRadioGroup,
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
        urlImport: {
            required: true,
            type: String,
        },
        urlCetak: {
            required: true,
            type: String,
        },
    },
    data() {
        return {
            items: [],
            companies:[],
            bulans:[],
            tahuns:[],
            filterOptions: [
                {val : '1', text : 'ALL'},
                {val : '2', text : 'SMM'},
                {val : '3', text : 'ATM'},
            ],
            form: {
                bulan : null,
                tahun : null,
                company : null,
                date_created : new Date(),
                periode_awal : new Date(),
                periode_akhir : new Date(),
                url: null,
                file: null,
                method: 'POST'
            },
            identity: null,
            queryParams: {
                per_page: 15,
                keyword: null,
                filter: 1,
                page: 1,
            },
            totalRows: 0,
            isBusy: false,
            fields: [
                { key: 'index', label: '#', thStyle: 'text-align: center; width: 35px;', tdClass: 'custom-cell text-center' },
                { key: 'kantor', label: 'Kantor', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nik', label: 'NIK', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nama', label: 'Nama', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'pesangon', label: 'Gaji Pokok', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'periode_bulan', label: 'Bulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'periode_tahun', label: 'Tahun', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
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
    mounted () {
        this.loadBulans()
        this.loadTahuns()
    },
    filters: {
        moment_date: function (date) {
            return moment(date).format('DD/MM/YYYY');
        }
    },
    methods: {
        uploadExcell(event) {
            this.form.file = event.target.files[0]
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
        loadBulans(){
            axios.get('/recruitment/payroll/api/v1/find-bulan')
            .then(response => {
                this.bulans = response.data.data
                this.bulans.unshift({ value: null, text: 'Pilih Bulan' })
            })
            .catch(error => console.log(error));
        },
        loadTahuns(){
            axios.get('/recruitment/payroll/api/v1/find-tahun')
            .then(response => {
                this.tahuns = response.data.data
                this.tahuns.unshift({ value: null, text: 'Pilih Tahun' })
            })
            .catch(error => console.log(error));
        },
        UploadPesangon() {
            this.modalTitle = 'Upload Pesangon'
            this.form.url = this.urlImport
            this.form.bulan = null
            this.form.tahun = null
            this.form.periode_awal = null
            this.form.periode_akhir = null
            this.form.method = 'POST'
            this.$refs['bv-modal-upload'].show()
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
                    date_created : moment(this.form.date_created).format('YYYY-MM-DD'),
                    periode_awal : moment(this.form.periode_awal).format('YYYY-MM-DD'),
                    periode_akhir : moment(this.form.periode_akhir).format('YYYY-MM-DD'),
                    company : this.form.company,
                }
            })
                .then(response => {
                    this.hideModal()
                    this.queryParams.keyword = this.form.bulan
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
        CetakSlip() {
            this.modalTitle = 'Download Slip Gaji'
            this.form.url = this.urlCetak
            this.form.method = 'GET'
            console.log('link:'+this.form.url);
            window.open(this.form.url)
        },
        async submitFormUpload() {
            // const isValid = await this.$refs.observer.validate();
            // if (!isValid) {
            //     return
            // }

            let form = new FormData()
            form.append('file', this.form.file)
            form.append('bulan', this.form.bulan)
            form.append('tahun', this.form.tahun)
            // form.append('date_created', moment(this.form.date_created).format('YYYY-MM-DD'))

            axios({
                method: this.form.method,
                url: this.form.url,
                data: form,
                headers:{
                    "Content-Type":"multipart/form-data"
                }
            })
                .then(response => {
                    this.hideModalUpload()
                    // this.queryParams.keyword = this.form.bulan
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
                        this.hideModalUpload()
                        this.$message({
                            showClose: true,
                            message: 'Oops, Sepertinya error nih, mohon dicoba kembali ya!',
                            type: 'error'
                        });
                    }
                })
        },
        hideModal() {
            this.$refs['bv-modal-example'].hide()
        },
        hideModalDetail() {
            this.$refs['bv-modaldetail-example'].hide()
        },
        hideModalUpload() {
            this.$refs['bv-modal-upload'].hide()
        },
        search() {
            this.fetchData()
            this.$refs.table.refresh()
        },
    }
}
</script>
