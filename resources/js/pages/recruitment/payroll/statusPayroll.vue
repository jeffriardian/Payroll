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
        </div>

        <div class="card">
            <!-- Card header -->
            <div class="card-header" style="background-color: rgb(232, 232, 232) !important;">
                <div class="row d-flex align-items-center">
                    <!-- Title Bar -->
                    <div class="col-12 col-md-8">
                        <h3 class="card-title">
                            <i class="nav-icon fas fa-layer-group"></i> Data Payroll
                        </h3>
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

                    <!-- Action -->
                    <!-- <template v-slot:cell(action)="data"> -->
                        <!-- Edit Button -->
                        <!-- <a href="#" @click.prevent="detailPayroll(data.item)" class="action-button text-secondary" data-toggle="tooltip" title="Data PPH 21">
                            <i class="far fa-address-card"></i>
                        </a>
                    </template> -->
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
    },
    data() {
        return {
            items: [],
            form: {
                url: null,
                file: null,
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
                { key: 'nik', label: 'Bulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nama', label: 'Status', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nik', label: 'Bulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'nama', label: 'Status', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
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
        }
    },
    mounted () {
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
    }
}
</script>
