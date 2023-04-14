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
                            <i class="nav-icon fas fa-layer-group"></i> Data Payroll
                        </h3>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-12 col-md-8 ">
                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Upload Absensi Staff"
                            @click.prevent="UploadAbsensiStaff()"
                        >
                            <i class="fas fa-upload"></i> Upload Absensi Staff
                        </a>
                        <button
                            type="button"
                            class="btn btn-default dropdown-toggle dropdown-icon"
                            data-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="fas fa-file-export"></i> Download
                            <div class="dropdown-menu" role="menu" x-placement="bottom-start">
                                <a class="dropdown-item" href="#" @click="ExportRekapanSmm()">Download Rekapan (SMM)</a>
                                <a class="dropdown-item" href="#" @click="ExportRekapanAtm()">Download Rekapan (ATM)</a>
                                <a class="dropdown-item" href="#" @click="ExportPermataSmm()">Download Format Permata (SMM)</a>
                                <a class="dropdown-item" href="#" @click="ExportPermataAtm()">Download Format Permata (ATM)</a>
                                <a class="dropdown-item" href="#" @click="ExportExcellSmm()">Download Excell (SMM)</a>
                                <a class="dropdown-item" href="#" @click="ExportExcellAtm()">Download Excell (ATM)</a>
                                <a class="dropdown-item" href="#" @click="ExportFullSmm()">Download Full (SMM)</a>
                                <a class="dropdown-item" href="#" @click="ExportFullAtm()">Download Full (ATM)</a>
                                <a class="dropdown-item" href="#" @click="ExportPPHSmm()">Download PPH (SMM)</a>
                                <a class="dropdown-item" href="#" @click="ExportPPHAtm()">Download PPH (ATM)</a>
                            </div>
                        </button>
                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Print Slip Gaji"
                            @click="CetakSlip()"
                        >
                            <i class="fas fa-print"></i> Print Slip Gaji
                        </a>
                    </div>
                    <!-- Action Bar -->
                    <div class="col-12 col-md-4 text-right text-md-right">
                        <!-- <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Simulasi Hitung Payroll"
                            @click.prevent="SimulasiHitungPayroll()"
                        >
                            <i class="fas fa-plus-square"></i> Simulasi Hitung Payroll
                        </a> -->

                        <b-form-radio-group
                            v-model="queryParams.filter"
                            :options="filterOptions"
                            value-field="val"
                            text-field="text"
                            @input="search()"
                        ></b-form-radio-group>
                        <b-form-select
                            v-model="queryParams.month"
                            :options="monthOptions"
                            value-field="val"
                            text-field="text"
                            @input="search()"
                        ></b-form-select>
                        <!-- <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Download Payroll-PPH Staff"
                            @click.prevent="DownloadPayrollStaff()"
                        >
                            <i class="fas fa-plus-square"></i> Download Payroll-PPH Staff
                        </a>
                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Proses Payroll"
                            @click.prevent="prosesPayroll()"
                        >
                            <i class="fas fa-plus-square"></i> Payroll Proccess
                        </a>-->
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

        <!-- Detail Payroll Modal -->
        <b-modal ref="bv-modaldetail-example" :title="modalTitle" footer-class="p-2" size="xl">
            <div class="d-block text-center px-3 py-2">
                <!-- <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()"> -->
                <validation-observer ref="observer" tag="form" @submit.prevent="submitForm()">
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

                        <!-- Data PPH 21 -->
                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Data PPH 21</center></h6>
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
                                            v-model="form.pph21_terutang_sebulan"
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

        <!-- Simulasi Hitung Payroll -->
        <b-modal ref="bv-modal-simulasi" :title="modalTitle" footer-class="p-2" size="xl">
            <div class="d-block text-center px-3 py-2">
                <!-- <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()"> -->
                <validation-observer ref="observer" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <div class="row bg-light">
                            <div class="col-md-12 p-2" style="margin-bottom:-20px;">
                                <h6 class="form-title text-left text-bold pb-2"><center>Pendapatan</center></h6>
                            </div>
                        </div>
                        <div class="row bg-light">
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Gaji Pokok -->
                                <b-form-group id="group-gapok" ref="gapok" label="Gaji Pokok :" label-for="gapok" class="text-center">
                                    <validation-provider mode="passive" name="gapok" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="gapok"
                                            v-model="form.gapok"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Gaji Pokok"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Premi Hadir -->
                                <b-form-group id="group-premihadir" ref="premihadir" label="Premi Hadir :" label-for="premihadir" class="text-center">
                                    <validation-provider mode="passive" name="premihadir" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="premihadir"
                                            v-model="form.premihadir"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Premi Hadir"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Premi Prestasi -->
                                <b-form-group id="group-premiprestasi" ref="premiprestasi" label="Premi Prestasi :" label-for="premiprestasi" class="text-center">
                                    <validation-provider mode="passive" name="premiprestasi" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="premiprestasi"
                                            v-model="form.premiprestasi"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Premi Prestasi"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Bruto -->
                                <b-form-group id="group-bruto" ref="bruto" label="Bruto :" label-for="bruto" class="text-center">
                                    <validation-provider mode="passive" name="bruto" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bruto"
                                            v-model="form.bruto"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Bruto"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>
                        <div class="row bg-light">
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Hari Kerja -->
                                <b-form-group id="group-harikerja" ref="harikerja" label="Hari Kerja :" label-for="harikerja" class="text-center">
                                    <validation-provider mode="passive" name="harikerja" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="harikerja"
                                            v-model="form.harikerja"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="harikerja"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Sakit -->
                                <b-form-group id="group-sakit" ref="sakit" label="Sakit :" label-for="sakit" class="text-center">
                                    <validation-provider mode="passive" name="sakit" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="sakit"
                                            v-model="form.sakit"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Sakit"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Ijin -->
                                <b-form-group id="group-ijin" ref="ijin" label="Ijin :" label-for="ijin" class="text-center">
                                    <validation-provider mode="passive" name="ijin" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="ijin"
                                            v-model="form.ijin"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Ijin"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Alpa -->
                                <b-form-group id="group-alpa" ref="alpa" label="Alpa :" label-for="alpa" class="text-center">
                                    <validation-provider mode="passive" name="alpa" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="alpa"
                                            v-model="form.alpa"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Alpa"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Cuti -->
                                <b-form-group id="group-cuti" ref="cuti" label="Cuti :" label-for="cuti" class="text-center">
                                    <validation-provider mode="passive" name="cuti" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="cuti"
                                            v-model="form.cuti"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Cuti"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- OFF -->
                                <b-form-group id="group-off" ref="off" label="OFF :" label-for="off" class="text-center">
                                    <validation-provider mode="passive" name="off" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="off"
                                            v-model="form.off"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="OFF"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- ST -->
                                <b-form-group id="group-st" ref="st" label="ST :" label-for="st" class="text-center">
                                    <validation-provider mode="passive" name="st" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="st"
                                            v-model="form.st"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="ST"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Telat Kurang 2 Jam -->
                                <b-form-group id="group-telatkurang2jam" ref="telatkurang2jam" label="Telat <= 2 Jam :" label-for="telatkurang2jam" class="text-center">
                                    <validation-provider mode="passive" name="telatkurang2jam" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="telatkurang2jam"
                                            v-model="form.telatkurang2jam"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Telat <= 2 Jam"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Telat Lebih 2 Jam -->
                                <b-form-group id="group-telatlebih2jam" ref="telatlebih2jam" label="Telat > 2 Jam :" label-for="telatlebih2jam" class="text-center">
                                    <validation-provider mode="passive" name="telatlebih2jam" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="telatlebih2jam"
                                            v-model="form.telatlebih2jam"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Telat > 2 Jam"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- 1/2 Hari -->
                                <b-form-group id="group-setengahhari" ref="setengahhari" label="1/2 Hari :" label-for="setengahhari" class="text-center">
                                    <validation-provider mode="passive" name="setengahhari" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="setengahhari"
                                            v-model="form.setengahhari"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="1/2 Hari"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Nilai Potongan Absensi</center></h6>
                                <!-- Total Potongan Absensi -->
                                <b-form-group id="group-totalpotonganabsensi" label="Total Potongan Absensi :" label-for="totalpotonganabsensi" class="text-center">
                                    <validation-provider mode="passive" name="totalpotonganabsensi" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totalpotonganabsensi"
                                            v-model="form.totalpotonganabsensi"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Total Potongan Absensi"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Sakit -->
                                <b-form-group id="group-nilaisakit" ref="nilaisakit" label="Sakit :" label-for="nilaisakit" class="text-center">
                                    <validation-provider mode="passive" name="nilaisakit" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaisakit"
                                            v-model="form.nilaisakit"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Sakit"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Ijin -->
                                <b-form-group id="group-nilaiijin" ref="nilaiijin" label="Ijin :" label-for="nilaiijin" class="text-center">
                                    <validation-provider mode="passive" name="nilaiijin" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaiijin"
                                            v-model="form.nilaiijin"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="nilaiijin"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Alpa -->
                                <b-form-group id="group-nilaialpa" ref="nilaialpa" label="Alpa :" label-for="nilaialpa" class="text-center">
                                    <validation-provider mode="passive" name="nilaialpa" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaialpa"
                                            v-model="form.nilaialpa"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Alpa"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Cuti -->
                                <b-form-group id="group-nilaicuti" ref="nilaicuti" label="Cuti :" label-for="nilaicuti" class="text-center">
                                    <validation-provider mode="passive" name="nilaicuti" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaicuti"
                                            v-model="form.nilaicuti"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Cuti"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- OFF -->
                                <b-form-group id="group-nilaioff" ref="nilaioff" label="OFF :" label-for="nilaioff" class="text-center">
                                    <validation-provider mode="passive" name="off" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaioff"
                                            v-model="form.nilaioff"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="OFF"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Nilai ST -->
                                <b-form-group id="group-nilaist" ref="nilaist" label="Nilai ST :" label-for="nilaist" class="text-center">
                                    <validation-provider mode="passive" name="st" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaist"
                                            v-model="form.nilaist"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Nilai STr"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Telat Kurang 2 Jam -->
                                <b-form-group id="group-nilaitelatkurang2jam" ref="nilaitelatkurang2jam" label="Telat <= 2 Jam :" label-for="nilaitelatkurang2jam" class="text-center">
                                    <validation-provider mode="passive" name="nilaitelatkurang2jam" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaitelatkurang2jam"
                                            v-model="form.nilaitelatkurang2jam"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Telat <= 2 Jam"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Telat Lebih 2 Jam -->
                                <b-form-group id="group-nilaitelatlebih2jam" ref="nilaitelatlebih2jam" label="Telat > 2 Jam :" label-for="nilaitelatlebih2jam" class="text-center">
                                    <validation-provider mode="passive" name="nilaitelatlebih2jam" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaitelatlebih2jam"
                                            v-model="form.nilaitelatlebih2jam"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Telat > 2 Jam"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- 1/2 Hari -->
                                <b-form-group id="group-nilaisetengahhari" ref="nilaisetengahhari" label="1/2 Hari :" label-for="nilaisetengahhari" class="text-center">
                                    <validation-provider mode="passive" name="nilaisetengahhari" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilaisetengahhari"
                                            v-model="form.nilaisetengahhari"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="1/2 Hari"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Lembur</center></h6>
                                <!-- Total Lembur -->
                                <b-form-group id="group-totallembur" label="Total Lembur :" label-for="totallembur" class="text-center">
                                    <validation-provider mode="passive" name="lembur" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totallembur"
                                            v-model="form.totallembur"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Total Lembur"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Lembur 1 -->
                                <b-form-group id="group-lembur1" ref="lembur1" label="Lembur 1 :" label-for="lembur1" class="text-center">
                                    <validation-provider mode="passive" name="lembur1" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="lembur1"
                                            v-model="form.lembur1"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 1"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Tarif Lembur 1 -->
                                <b-form-group id="group-tariflembur1" ref="tariflembur1" label="Tarif Lembur 1 :" label-for="tariflembur1" class="text-center">
                                    <validation-provider mode="passive" name="tariflembur1" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tariflembur1"
                                            v-model="form.tariflembur1"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 1"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Nilai Lembur 1 -->
                                <b-form-group id="group-nilailembur1" ref="nilailembur1" label="Nilai Lembur 1 :" label-for="nilailembur1" class="text-center">
                                    <validation-provider mode="passive" name="nilailembur1" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilailembur1"
                                            v-model="form.nilailembur1"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 1"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Lembur 2 -->
                                <b-form-group id="group-lembur2" ref="lembur2" label="Lembur 2 :" label-for="lembur2" class="text-center">
                                    <validation-provider mode="passive" name="lembur2" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="lembur2"
                                            v-model="form.lembur2"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Tarif Lembur 2 -->
                                <b-form-group id="group-tariflembur2" ref="tariflembur2" label="Tarif Lembur 2 :" label-for="tariflembur2" class="text-center">
                                    <validation-provider mode="passive" name="tariflembur2" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tariflembur2"
                                            v-model="form.tariflembur2"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            require
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Nilai Lembur 2 -->
                                <b-form-group id="group-nilailembur2" ref="nilailembur2" label="Nilai Lembur 2 :" label-for="nilailembur2" class="text-center">
                                    <validation-provider mode="passive" name="nilailembur2" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilailembur2"
                                            v-model="form.nilailembur2"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Lembur 2 Minggu -->
                                <b-form-group id="group-lembur2minggu" ref="lembur2minggu" label="Lembur 2 Minggu :" label-for="lembur2minggu" class="text-center">
                                    <validation-provider mode="passive" name="lembur2minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="lembur2minggu"
                                            v-model="form.lembur2minggu"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Tarif Lembur 2 Minggu -->
                                <b-form-group id="group-tariflembur2minggu" ref="tariflembur2minggu" label="Tarif Lembur 2 Minggu :" label-for="tariflembur2minggu" class="text-center">
                                    <validation-provider mode="passive" name="tariflembur2minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tariflembur2minggu"
                                            v-model="form.tariflembur2minggu"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Nilai Lembur 2 Minggu -->
                                <b-form-group id="group-nilailembur2minggu" ref="nilailembur2minggu" label="Nilai Lembur 2 Minggu :" label-for="nilailembur2minggu" class="text-center">
                                    <validation-provider mode="passive" name="nilailembur2minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilailembur2minggu"
                                            v-model="form.nilailembur2minggu"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 2"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Lembur 3 Minggu -->
                                <b-form-group id="group-lembur3minggu" ref="lembur3minggu" label="Lembur 3 Minggu :" label-for="lembur3minggu" class="text-center">
                                    <validation-provider mode="passive" name="lembur3minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="lembur3minggu"
                                            v-model="form.lembur3minggu"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 3"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Tarif Lembur 3 Minggu -->
                                <b-form-group id="group-tariflembur3minggu" ref="tariflembur3minggu" label="Tarif Lembur 3 Minggu :" label-for="tariflembur3minggu" class="text-center">
                                    <validation-provider mode="passive" name="tariflembur3minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tariflembur3minggu"
                                            v-model="form.tariflembur3minggu"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 3"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Nilai Lembur 3 Minggu -->
                                <b-form-group id="group-nilailembur3minggu" ref="nilailembur3minggu" label="Nilai Lembur 3 Minggu :" label-for="nilailembur3minggu" class="text-center">
                                    <validation-provider mode="passive" name="nilailembur3minggu" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nilailembur3minggu"
                                            v-model="form.nilailembur3minggu"
                                            type="text"
                                            disabled
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Lembur 3"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>
                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Total Potongan</center></h6>
                                <!-- Total Potongan -->
                                <b-form-group id="group-totalpotongan" ref="totalpotongan" label="Total Potongan :" label-for="totalpotongan" class="text-center">
                                    <validation-provider mode="passive" name="totalpotongan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totalpotongan"
                                            v-model="form.totalpotongan"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            disabled
                                            placeholder="Total Potongan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- Koperasi -->
                                <b-form-group id="group-koperasi" ref="koperasi" label="Koperasi :" label-for="koperasi" class="text-center">
                                    <validation-provider mode="passive" name="koperasi" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="koperasi"
                                            v-model="form.koperasi"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="koperasi"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- BPJSTK -->
                                <b-form-group id="group-bpjstk" ref="bpjstk" label="BPJS TK :" label-for="bpjstk" class="text-center">
                                    <validation-provider mode="passive" name="bpjstk" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bpjstk"
                                            v-model="form.bpjstk"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="BPJS TK"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-4 p-2" style="margin-bottom:-20px;">
                                <!-- BPJS Kes -->
                                <b-form-group id="group-bpjskes" ref="bpjskes" label="BPJS Kes :" label-for="bpjskes" class="text-center">
                                    <validation-provider mode="passive" name="bpjskes" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bpjskes"
                                            v-model="form.bpjskes"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="BPJS Kes"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <!-- Gaji Bersih -->
                                <b-form-group id="group-gajibersih" label="Gaji Bersih :" label-for="gajibersih" class="text-center">
                                    <validation-provider mode="passive" name="gajibersih" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="gajibersih"
                                            v-model="form.gajibersih"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Gaji Bersih"
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
                    <button class="btn btn-sm btn-outline-danger border-0" @click="hideModalSimulasi()">
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
        urlRekapanSmm: {
            required: true,
            type: String,
        },
        urlRekapanAtm: {
            required: true,
            type: String,
        },
        urlPermataSmm: {
            required: true,
            type: String,
        },
        urlPermataAtm: {
            required: true,
            type: String,
        },
        urlExcellSmm: {
            required: true,
            type: String,
        },
        urlExcellAtm: {
            required: true,
            type: String,
        },
        urlFullSmm: {
            required: true,
            type: String,
        },
        urlFullAtm: {
            required: true,
            type: String,
        },
        urlPphSmm: {
            required: true,
            type: String,
        },
        urlPphAtm: {
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
            monthOptions: [
                {val : '1', text : 'January'},
                {val : '2', text : 'February'},
                {val : '3', text : 'March'},
                {val : '4', text : 'April'},
                {val : '5', text : 'May'},
                {val : '6', text : 'June'},
                {val : '7', text : 'July'},
                {val : '8', text : 'August'},
                {val : '9', text : 'September'},
                {val : '10', text : 'October'},
                {val : '11', text : 'November'},
                {val : '12', text : 'December'},
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
                { key: 'bagian', label: 'Bagian', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'gapok', label: 'Gaji Pokok', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'premihadir', label: 'Uang Hadir', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'gaji', label: 'Gaji', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'totallembur', label: 'Total Lembur', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'bruto', label: 'Bruto', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'totalpotonganabsensi', label: 'Potongan Absensi', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'potkoperasi', label: 'Koperasi', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'potjhtkar', label: 'JHT', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'potpensiunkar', label: 'Pensiun', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'bpjskeskar', label: 'BPJS Kesehatan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'totalpotongan', label: 'Total Potongan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'netto', label: 'Netto', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' }
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
        prosesPayroll() {
            this.modalTitle = 'Proses Payroll'
            this.form.url = this.urlStore
            this.form.date_created = null
            this.form.periode_awal = null
            this.form.periode_akhir = null
            this.form.company = null
            this.form.method = 'POST'
            this.$refs['bv-modal-example'].show()
        },
        UploadAbsensiStaff() {
            this.modalTitle = 'Upload Payroll'
            this.form.url = this.urlImport
            this.form.bulan = null
            this.form.tahun = null
            this.form.periode_awal = null
            this.form.periode_akhir = null
            this.form.method = 'POST'
            this.$refs['bv-modal-upload'].show()
        },
        detailPayroll(item) {
            this.modalTitle = 'Data Payroll'
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
            this.form.pph21_terutang_sebulan = item.pph21_terutang_sebulan
            this.getGaji()
            this.getJaminan()
            this.getPotongan()
            this.form.method = 'PUT'
            this.$refs['bv-modaldetail-example'].show()
        },
        SimulasiHitungPayroll() {
            this.modalTitle = 'Simulasi Hitung Payroll'
            this.form.gapok             = '0'
            this.form.premihadir        = '0'
            this.form.premiprestasi     = '0'
            this.form.bruto             = '0'
            this.form.jumlahhari        = '0'
            this.form.harikerja         = '0'
            this.form.sakit             = '0'
            this.form.ijin              = '0'
            this.form.alpa              = '0'
            this.form.cuti              = '0'
            this.form.off               = '0'
            this.form.st                = '0'
            this.form.telatkurang2jam   = '0'
            this.form.telatlebih2jam    = '0'
            this.form.setengahhari      = '0'
            this.form.totalpotonganabsensi   = '0'
            this.form.nilaisakit             = '0'
            this.form.nilaiijin              = '0'
            this.form.nilaialpa              = '0'
            this.form.nilaicuti              = '0'
            this.form.nilaioff               = '0'
            this.form.nilaist                = '0'
            this.form.nilaitelatkurang2jam   = '0'
            this.form.nilaitelatlebih2jam    = '0'
            this.form.nilaisetengahhari      = '0'
            this.form.totallembur            = '0'
            this.form.lembur1                = '0'
            this.form.tariflembur1           = '8671'
            this.form.nilailembur1           = '0'
            this.form.lembur2                = '0'
            this.form.tariflembur2           = '11561'
            this.form.nilailembur2           = '0'
            this.form.lembur2minggu          = '0'
            this.form.tariflembur2minggu     = '11561'
            this.form.nilailembur2minggu     = '0'
            this.form.lembur3minggu          = '0'
            this.form.tariflembur3minggu     = '17341'
            this.form.nilailembur3minggu     = '0'
            this.form.totalpotongan          = '0'
            this.form.koperasi               = '0'
            this.form.bpjstk                 = '86962'
            this.form.bpjskes                = '31454'
            this.form.gajibersih             = '0'
            this.form.method = 'POST'
            this.$refs['bv-modal-simulasi'].show()
        },

        OnChangeHitung(){
            this.form.bruto = parseInt(this.form.gapok)+parseInt(this.form.premihadir)+parseInt(this.form.premiprestasi)

            this.form.nilaisakit = parseInt(((this.form.premihadir)/26)*(this.form.sakit))
            this.form.nilaiijin = parseInt(parseInt((parseInt(this.form.gapok)+parseInt(this.form.premihadir))/26)*(this.form.ijin))
            this.form.nilaialpa = parseInt(parseInt((parseInt(this.form.gapok)+parseInt(this.form.premihadir))/26)*(this.form.alpa))
            this.form.nilaioff = parseInt(parseInt((parseInt(this.form.gapok)+parseInt(this.form.premihadir))/26)*(this.form.off))
            this.form.nilaist = parseInt(parseInt((parseInt(this.form.gapok)+parseInt(this.form.premihadir))/26)*(this.form.st))
            this.form.nilaitelatlebih2jam = parseInt(((this.form.gapok)/26/2)*(this.form.telatlebih2jam))
            this.form.nilaisetengahhari = parseInt(((this.form.gapok)/26/2)*(this.form.setengahhari))
            this.form.totalpotonganabsensi = parseInt((this.form.nilaisakit)+(this.form.nilaiijin)+(this.form.nilaialpa)+
            (this.form.nilaioff)+(this.form.nilaist)+(this.form.nilaitelatlebih2jam)+(this.form.nilaisetengahhari))

            this.form.tariflembur1 = parseInt(((this.form.gapok)*1.5)/173)
            this.form.tariflembur2 = parseInt(((this.form.gapok)*2)/173)
            this.form.tariflembur2minggu = parseInt(((this.form.gapok)*2)/173)
            this.form.tariflembur3minggu = parseInt(((this.form.gapok)*3)/173)

            this.form.nilailembur1 = parseInt(this.form.lembur1*this.form.tariflembur1)
            this.form.nilailembur2 = parseInt(this.form.lembur2*this.form.tariflembur2)
            this.form.nilailembur2minggu = parseInt(this.form.lembur2minggu*this.form.tariflembur2minggu)
            this.form.nilailembur3minggu = parseInt(this.form.lembur3minggu*this.form.tariflembur3minggu)
            this.form.totallembur = parseInt(this.form.nilailembur1)+parseInt(this.form.nilailembur2)+parseInt(this.form.nilailembur2minggu)+
            parseInt(this.form.nilailembur3minggu)

            this.form.totalpotongan = parseInt(this.form.totalpotonganabsensi)+parseInt(this.form.koperasi)+parseInt(this.form.bpjstk)+
            parseInt(this.form.bpjskes)

            this.form.gajibersih = parseInt(this.form.bruto)+parseInt(this.form.totallembur)-parseInt(this.form.totalpotongan)
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
        ExportRekapanSmm() {
            window.open(this.urlRekapanSmm, 'urlRekapanSMM')
        },
        ExportRekapanAtm()
        {
            window.open(this.urlRekapanAtm+"?company=ALL", 'urlRekapanATM')
        },
        ExportPermataSmm() {
            window.open(this.urlPermataSmm, 'urlPermataSMM')
        },
        ExportPermataAtm() {
            window.open(this.urlPermataAtm, 'urlPermataATM')
        },
        ExportExcellSmm() {
            window.open(this.urlExcellSmm, 'urlExcellSMM')
        },
        ExportExcellAtm() {
            window.open(this.urlExcellAtm, 'urlExcellATM')
        },
        ExportFullSmm() {
            window.open(this.urlFullSmm, 'urlFullSMM')
        },
        ExportFullAtm() {
            window.open(this.urlFullAtm, 'urlFullATM')
        },
        ExportPPHSmm() {
            window.open(this.urlPphSmm, 'urlPphSMM')
        },
        ExportPPHAtm() {
            window.open(this.urlPphAtm, 'urlPphATM')
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
