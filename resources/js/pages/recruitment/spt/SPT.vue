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
                            <i class="nav-icon fas fa-layer-group"></i> Data SPT
                        </h3>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-12 col-md-4 text-left text-md-right">
                        <button
                            type="button"
                            class="btn btn-default dropdown-toggle dropdown-icon"
                            data-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="fas fa-file-export"></i> Download
                            <div class="dropdown-menu" role="menu" x-placement="bottom-start">
                                <a class="dropdown-item" href="#" @click="ExportSPT()">Download SPT</a>
                            </div>
                        </button>

                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Proses SPT"
                            @click.prevent="prosesSpt()"
                        >
                            <i class="fas fa-plus-square"></i> Proses SPT
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

                    <template v-slot:cell(test_date)="data">
                        {{ data.value | moment_date }}
                    </template>

                    <!-- Action -->
                    <template v-slot:cell(action)="data">
                        <!-- Edit Button -->
                        <a href="#" @click.prevent="printSpt(data.item)" class="action-button text-secondary" data-toggle="tooltip" title="Data SPT">
                            <i class="far fa-address-card"></i>
                        </a>
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

        <!-- Proses SPT Modal -->
        <b-modal ref="bv-modal-example" :title="modalTitle" footer-class="p-2" size="m">
            <div class="d-block text-center px-4 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <div class="row bg-light">
                            <div class="col-md-12 p-2" style="margin-bottom:-20px;">
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
                    <button type="submit" class="d-none">Kirim</button>
                    </b-form>
                </validation-observer>
            </div>

            <template v-slot:modal-footer>
                <div class="w-100 m-0 text-center">
                    <div v-if="isProcess">Mohon Tunggu Sebentar <b-spinner class="ml-2" small></b-spinner></div>
                    <button class="btn btn-sm btn-primary" @click="submitForm()">
                        Kirim
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0" @click="hideModal()">
                        Batal
                    </button>
                </div>
            </template>
        </b-modal>

        <!-- Detail SPT Modal -->
        <b-modal ref="bv-modaldetail-example" :title="modalTitle" footer-class="p-2" size="xl">
            <div class="d-block text-center px-3 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Data Karyawan</center></h6>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- NRP -->
                                <b-form-group id="group-nrp" label="NRP :" label-for="nrp" class="text-center">
                                    <validation-provider mode="passive" name="NRP" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nrp"
                                            v-model="form.nrp"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan NRP"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- NIK -->
                                <b-form-group id="group-nik" label="NIK :" label-for="nik" class="text-center">
                                    <validation-provider mode="passive" name="NIK" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nik"
                                            v-model="form.nik"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan NIK"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Name -->
                                <b-form-group id="group-name" label="Nama :" label-for="name" class="text-center">
                                    <validation-provider mode="passive" name="Nama" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Nama"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- PTKP Code -->
                                <b-form-group id="group-ptkp_code" label="Kode PTKP :" label-for="ptkp_code" class="text-center">
                                    <validation-provider mode="passive" name="First Nama" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="ptkp_code"
                                            v-model="form.ptkp_code"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Kode PTKP"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <!-- Item Pendapatan dan Potongan List -->
                        <div class="row bg-light">
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <div class="col-md-12 p-2 mt-3 rmb-20">
                                    <h6 class="form-title text-left text-bold pb-2"><center>Gaji</center></h6>
                                </div>

                                <div class="col-md-12 p-2 table-responsive-lg mb-0">
                                    <table class="table table-md">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="5%">#</th>
                                                <th scope="col" width="55%">Parameter</th>
                                                <th scope="col" width="40%">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in gaji" :key="item.index">
                                                <th scope="row">{{ index+1 }}</th>
                                                <td>{{ item.nama_parameter_pph }}</td>
                                                <td>{{ item.jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <div class="col-md-12 p-2 mt-3 rmb-20">
                                    <h6 class="form-title text-left text-bold pb-2"><center>Jaminan</center></h6>
                                </div>

                                <div class="col-md-12 p-2 table-responsive-lg mb-0">
                                    <table class="table table-md">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="5%">#</th>
                                                <th scope="col" width="55%">Parameter</th>
                                                <th scope="col" width="40%">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in jaminan" :key="item.index">
                                                <th scope="row">{{ index+1 }}</th>
                                                <td>{{ item.nama_parameter_pph }}</td>
                                                <td>{{ item.jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <div class="col-md-12 p-2 mt-3 rmb-20">
                                    <h6 class="form-title text-left text-bold pb-2"><center>Potongan</center></h6>
                                </div>

                                <div class="col-md-12 p-2 table-responsive-lg mb-0">
                                    <table class="table table-md">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="5%">#</th>
                                                <th scope="col" width="55%">Parameter</th>
                                                <th scope="col" width="40%">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in potongan" :key="item.index">
                                                <th scope="row">{{ index+1 }}</th>
                                                <td>{{ item.nama_parameter_pph }}</td>
                                                <td>{{ item.jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Data SPT -->
                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Data SPT</center></h6>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Gaji -->
                                <b-form-group id="group-gaji" label="Gaji :" label-for="gaji" class="text-center">
                                    <validation-provider mode="passive" name="Gaji" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="gaji"
                                            v-model="form.gaji"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Gaji"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Jaminan -->
                                <b-form-group id="group-jaminan" label="Jaminan :" label-for="jaminan" class="text-center">
                                    <validation-provider mode="passive" name="Jaminan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="jaminan"
                                            v-model="form.jaminan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Jaminan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Bruto -->
                                <b-form-group id="group-bruto" label="Bruto :" label-for="bruto" class="text-center">
                                    <validation-provider mode="passive" name="Bruto" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bruto"
                                            v-model="form.bruto"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Bruto"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Potongan -->
                                <b-form-group id="group-potongan" label="Potongan :" label-for="potongan" class="text-center">
                                    <validation-provider mode="passive" name="Potongan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="potongan"
                                            v-model="form.potongan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Potongan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Netto Sebulan -->
                                <b-form-group id="group-netto_sebulan" label="Netto (Sebulan) :" label-for="netto_sebulan" class="text-center">
                                    <validation-provider mode="passive" name="Netto (Sebulan)" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="netto_sebulan"
                                            v-model="form.netto_sebulan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Netto (Sebulan)"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- Netto Setahun -->
                                <b-form-group id="group-netto_setahun" label="Netto (Setahun) :" label-for="netto_setahun" class="text-center">
                                    <validation-provider mode="passive" name="Netto (Setahun)" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="netto_setahun"
                                            v-model="form.netto_setahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Netto (Setahun)"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- PTKP -->
                                <b-form-group id="group-ptkp" label="PTKP :" label-for="ptkp" class="text-center">
                                    <validation-provider mode="passive" name="PTKP" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="ptkp"
                                            v-model="form.ptkp"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PTKP"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- PKP -->
                                <b-form-group id="group-pkp" label="PKP :" label-for="pkp" class="text-center">
                                    <validation-provider mode="passive" name="PKP" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pkp"
                                            v-model="form.pkp"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PKP"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- PPH 21 Terutang Setahun -->
                                <b-form-group id="group-pph21_terutang_setahun" label="PPH 21 Terutang (Setahun) :" label-for="pph21_terutang_setahun" class="text-center">
                                    <validation-provider mode="passive" name="PPH 21 Terutang (Setahun)" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pph21_terutang_setahun"
                                            v-model="form.pph21_terutang_setahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PPH 21 Terutang (Setahun)"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-6 p-2" style="margin-bottom:-20px;">
                                <!-- PPH 21 Terutang Sebulan -->
                                <b-form-group id="group-pph21_terutang_sebulan" label="PPH 21 Terutang (Sebulan) :" label-for="pph21_terutang_sebulan" class="text-center">
                                    <validation-provider mode="passive" name="PPH 21 Terutang (Sebulan)" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pph21_terutang_sebulan"
                                            v-model="form.total_pph21_terutang_sebulan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PPH 21 Terutang (Sebulan)"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>
                    </b-form>
                </validation-observer>
            </div>

            <template v-slot:modal-footer>
                <div class="w-100 m-0 text-center">
                    <button class="btn btn-sm btn-outline-danger border-0" @click="hideModalDetail()">
                        Close
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
import { BModal, BTable, BPagination, BFormSelect, BForm, BFormGroup, BFormInput, BFormCheckbox, BFormRadioGroup, BSpinner } from 'bootstrap-vue'

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
        BSpinner,
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
        urlSpt: {
            required: true,
            type: String,
        },
    },
    data() {
        return {
            items: [],
            tahuns:[],
            gaji:[],
            jaminan:[],
            potongan:[],
            form: {
                tahun: null,
                gaji:[],
                jaminan:[],
                potongan:[],
                url: null,
                method: 'POST'
            },
            identity: null,
            queryParams: {
                per_page: 15,
                keyword: null,
                page: 1,
            },
            totalRows: 0,
            isBusy: false,
            fields: [
                { key: 'index', label: '#', thStyle: 'text-align: center; width: 35px;', tdClass: 'custom-cell text-center' },
                { key: 'nik', label: 'NIK', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nama', label: 'Nama', thStyle: 'text-align: center;', tdClass: 'custom-cell text-left' },
                { key: 'ptkp_code', label: 'Kode PTKP', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'gross_yearly', label: 'Gross Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'position_cost_yearly', label: 'Position Cost', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'jht_employee_yearly', label: 'JHT Employee Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'jp_employee_yearly', label: 'JP Employee Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'netto_yearly', label: 'Netto Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'ptkp', label: 'PTKP', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pkp', label: 'PKP', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pph_due', label: 'PPH Due Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pph_paid', label: 'PPH Paid Yearly', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'periode_tahun', label: 'Tahun', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'action', label: 'Aksi', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' }
            ],
            meta: [],
            modalTitle: undefined,
            pageOptions: [
                { text: 15, value: 15, disabled: false },
                { text: 25, value: 25, disabled: false },
                { text: 50, value: 50, disabled: false },
                { text: 100, value: 100, disabled: false },
            ],
            isProcess:false,
            progressValue:0
        }
    },
    mounted () {
        this.loadTahuns()
    },
    filters: {
        moment_date: function (date) {
            return moment(date).format('DD/MM/YYYY');
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
        loadTahuns(){
            axios.get('/recruitment/spt/api/v1/find-tahun')
            .then(response => {
                this.tahuns = response.data.data
                this.tahuns.unshift({ value: null, text: 'Pilih Tahun' })
            })
            .catch(error => console.log(error));
        },
        ExportSPT() {
            this.modalTitle = 'Download SPT'
            this.form.url = this.urlSpt
            this.form.method = 'GET'
            console.log(this.form.url);
            window.open(this.form.url)
        },
        prosesSpt() {
            this.modalTitle = 'Proses SPT'
            this.form.url = this.urlStore
            this.form.bulan = null
            this.form.tahun = null
            this.form.method = 'POST'
            this.$refs['bv-modal-example'].show()
        },
        detailSpt(item) {
            this.modalTitle = 'Data SPT'
            this.form.id = item.id
            this.form.nrp = item.nrp
            this.form.nik = item.nik
            this.form.name = item.nama
            this.form.ptkp_code = item.ptkp_code
            this.form.gaji = item.gaji
            this.form.jaminan = item.jaminan_perusahaan
            this.form.bruto = item.bruto
            this.form.potongan = item.potongan
            this.form.netto_sebulan = item.netto_sebulan
            this.form.netto_setahun = item.netto_setahun
            this.form.ptkp = item.ptkp
            this.form.pkp = item.pkp
            this.form.pph21_terutang_setahun = item.pph21_terutang_setahun
            this.form.total_pph21_terutang_sebulan = item.total_pph21_terutang_sebulan
            this.getGaji()
            this.getJaminan()
            this.getPotongan()
            this.form.method = 'PUT'
            this.$refs['bv-modaldetail-example'].show()
        },
        printSpt(item){
            axios.get(window.location.origin + `/recruitment/spt/api/v1/print-spt?id=${item.id}`)
            .then(response => {
                this.gaji = response.data.data
            })
            .catch(error => console.log(error));
        },
        getGaji(){
            axios.get(window.location.origin + `/recruitment/spt/api/v1/get-gaji?id=${this.form.id}`)
            .then(response => {
                this.gaji = response.data.data
            })
            .catch(error => console.log(error));
        },
        getJaminan(){
            axios.get(window.location.origin + `/recruitment/spt/api/v1/get-jaminan?id=${this.form.id}`)
            .then(response => {
                this.jaminan = response.data.data
            })
            .catch(error => console.log(error));
        },
        getPotongan(){
            axios.get(window.location.origin + `/recruitment/spt/api/v1/get-potongan?id=${this.form.id}`)
            .then(response => {
                this.potongan = response.data.data
            })
            .catch(error => console.log(error));
        },
        async submitForm() {
            const isValid = await this.$refs.observer.validate();
            if (!isValid) {
                return
            }
            this.isProcess = true;
            axios({
                method: this.form.method,
                url: this.form.url,
                data: {
                    bulan : this.form.bulan,
                    tahun : this.form.tahun,
                },

                onUploadProgress: (progressEvent) => {
                    const totalLength = progressEvent.lengthComputable ? progressEvent.total : progressEvent.target.getResponseHeader('content-length') || progressEvent.target.getResponseHeader('x-decompressed-content-length');
                    console.log("onUploadProgress", totalLength);
                    if (totalLength !== null) {
                        // this.progressValue(Math.round( (progressEvent.loaded * 100) / totalLength ));
                    }
                }

            })
                .then(response => {
                    this.isProcess = false;
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
        hideModal() {
            this.$refs['bv-modal-example'].hide()
        },
        hideModalDetail() {
            this.$refs['bv-modaldetail-example'].hide()
        },
        hideModalSimulasi() {
            this.$refs['bv-modal-simulasi'].hide()
        },
        search() {
            this.fetchData()
            this.$refs.table.refresh()
        },
    }
}
</script>
