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
                            <i class="nav-icon fas fa-layer-group"></i> Data PPH 21
                        </h3>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-12 col-md-4 text-left text-md-right">
                        <!-- <button
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
                        </button> -->

                        <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Simulasi Hitung PPH 21"
                            @click.prevent="SimulasiHitungPPH()"
                        >
                            <i class="fas fa-plus-square"></i> Simulasi Hitung PPH
                        </a>
                        <!-- <a
                            href="#"
                            class="btn btn-default modal-show"
                            data-toggle="tooltip"
                            title="Proses PPH"
                            @click.prevent="prosesPph()"
                        >
                            <i class="fas fa-plus-square"></i> Proses PPH
                        </a> -->
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
                        <a href="#" @click.prevent="detailPph(data.item)" class="action-button text-secondary" data-toggle="tooltip" title="Data PPH 21">
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

        <!-- Proses PPH Modal -->
        <b-modal ref="bv-modal-example" :title="modalTitle" footer-class="p-2" size="m">
            <div class="d-block text-center px-3 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
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

        <!-- Detail PPH Modal -->
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

        <!-- Simulasi Hitung PPH 21 -->
        <b-modal ref="bv-modal-simulasi" :title="modalTitle" footer-class="p-2" size="xl">
            <div class="d-block text-center px-3 py-2">
                <validation-observer ref="observer" v-slot="{ invalid }" tag="form" @submit.prevent="submitForm()">
                    <b-form @submit.prevent="submitForm">
                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Komponen Pendapatan</center></h6>
                            </div>
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
                                            placeholder="Masukan Gaji Pokok"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Premi Hadir -->
                                <b-form-group id="group-premi_hadir" ref="premi_hadir" label="Premi Hadir :" label-for="premi_hadir" class="text-center">
                                    <validation-provider mode="passive" name="premi_hadir" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="premi_hadir"
                                            v-model="form.premi_hadir"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Premi Hadir"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Premi Produksi -->
                                <b-form-group id="group-premi_produksi" ref="premi_produksi" label="Premi Produksi :" label-for="premi_produksi" class="text-center">
                                    <validation-provider mode="passive" name="premi_produksi" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="premi_produksi"
                                            v-model="form.premi_produksi"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Premi Produksi"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Tunjangan Jabatan -->
                                <b-form-group id="group-tunjangan_jabatan" ref="tunjangan_jabatan" label="Tunjangan Jabatan:" label-for="tunjangan_jabatan" class="text-center">
                                    <validation-provider mode="passive" name="tunjangan_jabatan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tunjangan_jabatan"
                                            v-model="form.tunjangan_jabatan"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Tunjangan Jabatan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Lembur -->
                                <b-form-group id="group-lembur" ref="lembur" label="Lembur :" label-for="lembur" class="text-center">
                                    <validation-provider mode="passive" name="lembur" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="lembur"
                                            v-model="form.lembur"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Lembur"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Tunjangan Lain -->
                                <b-form-group id="group-tunjangan_lain" ref="tunjangan_lain" label="Tunjangan Lain :" label-for="tunjangan_lain" class="text-center">
                                    <validation-provider mode="passive" name="tunjangan_lain" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tunjangan_lain"
                                            v-model="form.tunjangan_lain"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Tunjangan Lain"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Bonus -->
                                <b-form-group id="group-bonus" ref="bonus" label="Bonus :" label-for="bonus" class="text-center">
                                    <validation-provider mode="passive" name="bonus" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bonus"
                                            v-model="form.bonus"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Bonus"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;"> -->
                                <!-- THR -->
                                <!-- <b-form-group id="group-thr" ref="thr" label="THR :" label-for="thr" class="text-center">
                                    <validation-provider mode="passive" name="thr" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="thr"
                                            v-model="form.thr"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan THR"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group> -->
                            <!-- </div> -->
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- JKM -->
                                <b-form-group id="group-jkm" ref="jkm" label="JKM :" label-for="jkm" class="text-center">
                                    <validation-provider mode="passive" name="jkm" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="jkm"
                                            v-model="form.jkm"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan JKM"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- JKK -->
                                <b-form-group id="group-jkk" ref="jkk" label="JKK :" label-for="jkk" class="text-center">
                                    <validation-provider mode="passive" name="jkk" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="jkk"
                                            v-model="form.jkk"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan JKK"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- BPJS Kesehatan -->
                                <b-form-group id="group-bpjskesper" ref="bpjskesper" label="BPJS Kesehatan :" label-for="bpjskesper" class="text-center">
                                    <validation-provider mode="passive" name="bpjskesper" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="bpjskesper"
                                            v-model="form.bpjskesper"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan BPJS Kesehatan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <!-- Tunjangan PPH -->
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-tunjanganpph" label="Tunjangan PPH Awal :" label-for="tunjanganpph" class="text-center">
                                    <validation-provider mode="passive" name="Tunjangan PPH" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tunjanganpph"
                                            v-model="form.tunjanganpph"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Tunjangan PPH"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- THR -->
                                <b-form-group id="group-thr" ref="thr" label="THR :" label-for="thr" class="text-center">
                                    <validation-provider mode="passive" name="thr" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="thr"
                                            v-model="form.thr"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan THR"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Tunjangan PPH -->
                                <b-form-group id="group-tunjanganpph1" label="Tunjangan PPH :" label-for="tunjanganpph1" class="text-center">
                                    <validation-provider mode="passive" name="Tunjangan PPH" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="tunjanganpph1"
                                            v-model="form.tunjanganpph1"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Tunjangan PPH"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Total Bruto -->
                                <b-form-group id="group-totalbruto1" label="Total Bruto :" label-for="totalbruto1" class="text-center">
                                    <validation-provider mode="passive" name="Total Bruto" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totalbruto1"
                                            v-model="form.totalbruto1"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Total Bruto"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <!-- Total Bruto -->
                            <!-- <div class="col-md-6 p-2 mt-3 rmb-20">
                                <b-form-group id="group-totalbruto" label="Total Bruto Awal :" label-for="totalbruto" class="text-center">
                                    <validation-provider mode="passive" name="Total Bruto" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totalbruto"
                                            v-model="form.totalbruto"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Total Bruto"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Komponen Potongan</center></h6>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- BPJS Tenaga Kerja -->
                                <b-form-group id="group-jht" ref="jht" label="JHT :" label-for="jht" class="text-center">
                                    <validation-provider mode="passive" name="jht" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="jht"
                                            v-model="form.jht"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan BPJS Tenaga Kerja"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Jaminan Pensiun -->
                                <b-form-group id="group-pensiunkar" ref="pensiunkar" label="Jaminan Pensiun :" label-for="pensiunkar" class="text-center">
                                    <validation-provider mode="passive" name="pensiunkar" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pensiunkar"
                                            v-model="form.pensiunkar"
                                            type="text"
                                            v-on:change="OnChangeHitung()"
                                            required
                                            placeholder="Masukan Jaminan Pensiun"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <!-- Potongan Jabatan -->
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-potjabatan" label="Biaya Jabatan Awal :" label-for="potjabatan" class="text-center">
                                    <validation-provider mode="passive" name="Biaya Jabatan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="potjabatan"
                                            v-model="form.potjabatan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Biaya Jabatan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Potongan Jabatan -->
                                <b-form-group id="group-potjabatan1" label="Biaya Jabatan :" label-for="potjabatan1" class="text-center">
                                    <validation-provider mode="passive" name="Biaya Jabatan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="potjabatan1"
                                            v-model="form.potjabatan1"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Biaya Jabatan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Total Potongan -->
                                <b-form-group id="group-totalpotongan2" label="Total Potongan :" label-for="totalpotongan2" class="text-center">
                                    <validation-provider mode="passive" name="Total Potongan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="totalpotongan2"
                                            v-model="form.totalpotongan2"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Total Potongan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                        </div>

                        <div class="row bg-light">
                            <div class="col-md-12 p-2 mt-3 rmb-20">
                                <h6 class="form-title text-left text-bold pb-2"><center>Komponen Perhitungan PPH</center></h6>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-ptkp" label="Status Pajak :" label-for="ptkp" class="text-center">
                                    <validation-provider mode="passive" name="Status Pajak" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-select v-model="form.ptkp" :options="options" class="mb-3">
                                            <!-- <b-form-select-option value="C">Option C</b-form-select-option>
                                            <b-form-select-option value="D">Option D</b-form-select-option> -->
                                        </b-form-select>

                                        <!-- <div class="mt-3">Selected: <strong>{{ form.ptkp }}</strong></div> -->
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Bruto Setahun -->
                                <b-form-group id="group-brutosetahun" label="Bruto Setahun :" label-for="brutosetahun" class="text-center">
                                    <validation-provider mode="passive" name="Bruto Setahun" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="brutosetahun"
                                            v-model="form.brutosetahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Bruto Setahun"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <!-- Biaya Jabatan Setahun -->
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-biayajabatan" label="Biaya Jabatan Setahun :" label-for="biayajabatan" class="text-center">
                                    <validation-provider mode="passive" name="Biaya Jabatan" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="biayajabatan"
                                            v-model="form.biayajabatan"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Biaya Jabatan"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
                            <!-- Netto Setahun -->
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-pensiunsetahun" label="Iuran Pensiun Setahun :" label-for="pensiunsetahun" class="text-center">
                                    <validation-provider mode="passive" name="Iuran Pensiun" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pensiunsetahun"
                                            v-model="form.pensiunsetahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Iuran Pensiun"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Potongan Setahun -->
                                <b-form-group id="group-potongansetahun" label="Pengurang Setahun :" label-for="potongansetahun" class="text-center">
                                    <validation-provider mode="passive" name="Pengurang" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="potongansetahun"
                                            v-model="form.potongansetahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Pengurang"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- Netto Setahun -->
                                <b-form-group id="group-nettosetahun" label="Netto Setahun :" label-for="nettosetahun" class="text-center">
                                    <validation-provider mode="passive" name="Netto" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="nettosetahun"
                                            v-model="form.nettosetahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan Netto"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
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
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
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
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- PPH 21 Setahun -->
                                <b-form-group id="group-pph21setahun" label="PPH 21 Setahun :" label-for="pph21setahun" class="text-center">
                                    <validation-provider mode="passive" name="PPH 21" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pph21setahun"
                                            v-model="form.pph21setahun"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PPH 21"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <!-- PPH 21 -->
                                <b-form-group id="group-pph21" label="PPH 21 :" label-for="pph21" class="text-center">
                                    <validation-provider mode="passive" name="PPH 21" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="pph21"
                                            v-model="form.pph21"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Masukan PPH 21"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div>
                            <!-- <div class="col-md-3 p-2" style="margin-bottom:-20px;">
                                <b-form-group id="group-cek" label="Buat Ngecek :" label-for="cek" class="text-center">
                                    <validation-provider mode="passive" name="PPH 21" :rules="{required: true}" v-slot="{ errors }">
                                        <b-form-input
                                            id="cek"
                                            v-model="form.cek"
                                            type="text"
                                            required
                                            disabled
                                            placeholder="Buat Ngecek"
                                            maxlength="255"
                                        ></b-form-input>
                                        <span class="form-error-message" v-if="errors[0]">{{ errors[0] }}</span>
                                    </validation-provider>
                                </b-form-group>
                            </div> -->
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
    },
    data() {
        return {
            // ptkp: null,
            options: [
            { value: '54000000', text: 'TK/0' },
            { value: '58500000', text: 'K/0' },
            { value: '63000000', text: 'K/1' },
            { value: '67500000', text: 'K/2' },
            { value: '72000000', text: 'K/3' }
            ],
            items: [],
            bulans:[],
            tahuns:[],
            gaji:[],
            jaminan:[],
            potongan:[],
            form: {
                bulan: null,
                tahun: null,
                gaji:[],
                jaminan:[],
                potongan:[],
                url: null,
                ptkp: null,
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
                { key: 'gaji', label: 'Gaji', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'jaminan_perusahaan', label: 'Jaminan Perusahaan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
                { key: 'bruto', label: 'Bruto', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'potongan', label: 'Potongan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'netto_sebulan', label: 'Netto Sebulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'netto_setahun', label: 'Netto Setahun', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'ptkp', label: 'PTKP', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pkp', label: 'PKP', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pph21_terutang_setahun', label: 'PPH 21 Terutang Setahun', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'pph21_terutang_sebulan', label: 'PPH 21 Terutang Sebulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-right' },
                { key: 'periode_bulan', label: 'Bulan', thStyle: 'text-align: center;', tdClass: 'custom-cell text-center' },
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
        this.loadBulans()
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
        loadBulans(){
            axios.get('/recruitment/pph/api/v1/find-bulan')
            .then(response => {
                this.bulans = response.data.data
                this.bulans.unshift({ value: null, text: 'Pilih Bulan' })
            })
            .catch(error => console.log(error));
        },
        loadTahuns(){
            axios.get('/recruitment/pph/api/v1/find-tahun')
            .then(response => {
                this.tahuns = response.data.data
                this.tahuns.unshift({ value: null, text: 'Pilih Tahun' })
            })
            .catch(error => console.log(error));
        },
        prosesPph() {
            this.modalTitle = 'Proses PPH 21'
            this.form.url = this.urlStore
            this.form.bulan = null
            this.form.tahun = null
            this.form.method = 'POST'
            this.$refs['bv-modal-example'].show()
        },
        SimulasiHitungPPH() {
            this.modalTitle = 'Simulasi Hitung PPH 21'
            this.form.url = this.urlStore
            this.form.totalgaji         = '0'
            this.form.gapok             = '0'
            this.form.premi_hadir       = '0'
            this.form.premi_produksi    = '0'
            this.form.tunjangan_jabatan = '0'
            this.form.lembur            = '0'
            this.form.tunjangan_lain    = '0'
            this.form.bonus             = '0'
            this.form.thr               = '0'
            this.form.totaljaminan      = '0'
            this.form.jkm               = '8696'
            this.form.jkk               = '25799'
            this.form.bpjskesper        = '125817'
            this.form.totalbruto        = '0'
            this.form.totalbruto1       = '0'
            this.form.totalpotongan     = '0'
            this.form.totalpotongan1    = '0'
            this.form.jht               = '28987'
            this.form.pensiunkar        = '57975'
            this.form.potjabatan        = '0'
            this.form.potjabatan1       = '0'
            this.form.tunjanganpph      = '0'
            this.form.tunjanganpph1     = '0'
            this.form.ptkp              = '63000000'
            this.form.brutosetahun      = '0'
            this.form.brutosetahun1     = '0'
            this.form.potongansetahun   = '0'
            this.form.potongansetahun1  = '0'
            this.form.nettosetahun      = '0'
            this.form.nettosetahun1     = '0'
            this.form.pkp               = '0'
            this.form.pkp1              = '0'
            this.form.pph21setahun      = '0'
            this.form.pph21             = '0'
            this.form.totalpotongan     = '0'
            this.form.method = 'POST'
            this.$refs['bv-modal-simulasi'].show()
        },

        OnChangeHitung(){
            //Perhitungan PPH 21 Gaji
            var totalbruto, totalbrutothr, biayajabatanthr0, biayajabatangaji0, totalpotonganthr0, totalpotongangaji0;
            var brutosetahunthr0, brutosetahungaji0, potongansetahunthr0, potongansetahungaji0, nettosetahunthr0, nettosetahungaji0;
            var pkpthr0, pkpgaji0, tunjanganpphthr0, tunjanganpphgaji0;
            var biayajabatangaji, biayajabatanthr, totalpotonganthr, totalpotongangaji, potongansetahunthr, potongansetahungaji;
            var nettosetahunthr, nettosetahungaji, pkpthr, pkpgaji, tunjanganpphsetahunthr, tunjanganpphsetahungaji, tunjanganpph1;
            var brutosetahunthr, brutosetahungaji;

            //Tahap 1
            totalbruto = parseInt(this.form.gapok) + parseInt(this.form.premi_hadir) + parseInt(this.form.premi_produksi)
             + parseInt(this.form.tunjangan_jabatan) + parseInt(this.form.lembur) + parseInt(this.form.tunjangan_lain) +
             parseInt(this.form.bonus) + parseInt(this.form.jkm) + parseInt(this.form.jkk)+ parseInt(this.form.bpjskesper)

             totalbrutothr = 12*(parseInt(this.form.gapok) + parseInt(this.form.premi_hadir) + parseInt(this.form.premi_produksi)
             + parseInt(this.form.tunjangan_jabatan) + parseInt(this.form.lembur) + parseInt(this.form.tunjangan_lain) +
             parseInt(this.form.bonus) + parseInt(this.form.jkm) + parseInt(this.form.jkk)+ parseInt(this.form.bpjskesper))
             + parseInt(this.form.thr)

            if ((Math.round(0.05*(parseInt(totalbrutothr)))) >= 6000000)
            {
                biayajabatanthr0 = 6000000
            }
            else if ((Math.round(0.05*(parseInt(totalbrutothr)))) < 6000000)
            {
                biayajabatanthr0 = Math.round(0.05*(parseInt(totalbrutothr)))
            }

            if ((Math.round(0.05*(parseInt(totalbruto)))) >= 500000)
            {
                biayajabatangaji0 = 500000
            }
            else if ((Math.round(0.05*(parseInt(totalbruto)))) < 500000)
            {
                biayajabatangaji0 = Math.round(0.05*(parseInt(totalbruto)))
            }

            totalpotonganthr0 = 12*(parseInt(this.form.jht) + parseInt(this.form.pensiunkar)) + parseInt(biayajabatanthr0)
            totalpotongangaji0 = parseInt(this.form.jht) + parseInt(this.form.pensiunkar) + parseInt(biayajabatangaji0)

            brutosetahunthr0 = parseInt(totalbrutothr)
            brutosetahungaji0 = 12*(parseInt(totalbruto))

            potongansetahunthr0 = parseInt(totalpotonganthr0)
            potongansetahungaji0 = 12*(parseInt(totalpotongangaji0))

            nettosetahunthr0 = parseInt(brutosetahunthr0) - parseInt(potongansetahunthr0)
            nettosetahungaji0 = parseInt(brutosetahungaji0) - parseInt(potongansetahungaji0)

            pkpthr0 = parseInt(nettosetahunthr0) - parseInt(this.form.ptkp)
            pkpgaji0 = parseInt(nettosetahungaji0) - parseInt(this.form.ptkp)

            if (parseInt(pkpthr0) > 405000000)
            {
                tunjanganpphthr0 = Math.round(((parseInt(pkpthr0) - 405000000)*(30/70)+95000000))
            }
            if ((parseInt(pkpthr0) > 217500000) || (parseInt(pkpthr0) < 405000000))
            {
                tunjanganpphthr0 = Math.round(((parseInt(pkpthr0) - 217500000)*(25/75)+32500000))
            }
            if ((parseInt(pkpthr0) > 47500000) || (parseInt(pkpthr0) < 217500000))
            {
                tunjanganpphthr0 = Math.round(((parseInt(pkpthr0) - 47500000)*(15/85)+2500000))
            }
            if ((parseInt(pkpthr0) <= 47500000))
            {
                tunjanganpphthr0 = Math.round((parseInt(pkpthr0))*(5/95))
            }

            if (parseInt(pkpgaji0) > 405000000)
            {
                tunjanganpphgaji0 = Math.round(((parseInt(pkpgaji0) - 405000000)*(30/70)+95000000)/12)
            }
            if ((parseInt(pkpgaji0) > 217500000) || (parseInt(pkpgaji0) < 405000000))
            {
                tunjanganpphgaji0 = Math.round(((parseInt(pkpgaji0) - 217500000)*(25/75)+32500000)/12)
            }
            if ((parseInt(pkpgaji0) > 47500000) || (parseInt(pkpgaji0) < 217500000))
            {
                tunjanganpphgaji0 = Math.round(((parseInt(pkpgaji0) - 47500000)*(15/85)+2500000)/12)
            }
            if ((parseInt(pkpgaji0) <= 47500000))
            {
                tunjanganpphgaji0 = Math.round((parseInt(pkpgaji0))*(5/95)/12)
            }

            //Biaya Jabatan Tahap 2
            if ((Math.round(0.05*(parseInt(totalbrutothr)+parseInt(tunjanganpphthr0)))) >= 6000000)
            {
                biayajabatanthr = 6000000
            }
            else if ((Math.round(0.05*(parseInt(totalbrutothr)+parseInt(tunjanganpphthr0)))) < 6000000)
            {
                biayajabatanthr = Math.round(0.05*(parseInt(totalbrutothr)+parseInt(tunjanganpphthr0)))
            }

            if ((Math.round(0.05*(parseInt(totalbruto)+parseInt(tunjanganpphgaji0)))) >= 500000)
            {
                biayajabatangaji = 500000
            }
            else if ((Math.round(0.05*(parseInt(totalbruto)+parseInt(tunjanganpphgaji0)))) < 500000)
            {
                biayajabatangaji = Math.round(0.05*(parseInt(totalbruto)+parseInt(tunjanganpphgaji0)))
            }

            //Bruto Setahun Tahap 2
            brutosetahunthr = parseInt(totalbrutothr)
            brutosetahungaji = 12*(parseInt(totalbruto))

            //Total Potongan Tahap 2

            totalpotonganthr = 12*(parseInt(this.form.jht) + parseInt(this.form.pensiunkar)) + parseInt(biayajabatanthr)
            totalpotongangaji = parseInt(this.form.jht) + parseInt(this.form.pensiunkar) + parseInt(biayajabatangaji)

            //Potongan Setahun Tahap 2
            potongansetahunthr = parseInt(totalpotonganthr)
            potongansetahungaji = 12*(parseInt(totalpotongangaji))

            //Netto Setahun Tahap 2
            nettosetahunthr = parseInt(brutosetahunthr) - parseInt(potongansetahunthr)
            nettosetahungaji = parseInt(brutosetahungaji) - parseInt(potongansetahungaji)

            //PKP Tahap 2
            pkpthr = parseInt(nettosetahunthr) - parseInt(this.form.ptkp)
            pkpgaji = parseInt(nettosetahungaji) - parseInt(this.form.ptkp)

            if (parseInt(pkpthr) > 405000000)
            {
                tunjanganpphsetahunthr = Math.round(((parseInt(pkpthr) - 405000000)*(30/70)+95000000))
            }
            if ((parseInt(pkpthr) > 217500000) || (parseInt(pkpthr) < 405000000))
            {
                tunjanganpphsetahunthr = Math.round(((parseInt(pkpthr) - 217500000)*(25/75)+32500000))
            }
            if ((parseInt(pkpthr) > 47500000) || (parseInt(pkpthr) < 217500000))
            {
                tunjanganpphsetahunthr = Math.round(((parseInt(pkpthr) - 47500000)*(15/85)+2500000))
            }
            if ((parseInt(pkpthr) <= 47500000))
            {
                tunjanganpphsetahunthr = Math.round((parseInt(pkpthr))*(5/95))
            }

            if (parseInt(pkpgaji) > 405000000)
            {
                tunjanganpphsetahungaji = Math.round(((parseInt(pkpgaji) - 405000000)*(30/70)+95000000))
            }
            if ((parseInt(pkpgaji) > 217500000) || (parseInt(pkpgaji) < 405000000))
            {
                tunjanganpphsetahungaji = Math.round(((parseInt(pkpgaji) - 217500000)*(25/75)+32500000))
            }
            if ((parseInt(pkpgaji) > 47500000) || (parseInt(pkpgaji) < 217500000))
            {
                tunjanganpphsetahungaji = Math.round(((parseInt(pkpgaji) - 47500000)*(15/85)+2500000))
            }
            if ((parseInt(pkpgaji) <= 47500000))
            {
                tunjanganpphsetahungaji = Math.round((parseInt(pkpgaji))*(5/95))
            }

            if ((this.form.thr) > 0)
            {
                tunjanganpph1 = Math.round(tunjanganpphsetahunthr - tunjanganpphsetahungaji) + Math.round(tunjanganpphsetahungaji/12)
            }
            if ((this.form.thr) <= 0)
            {
                tunjanganpph1 = Math.round(tunjanganpphsetahungaji/12)
            }

            this.form.tunjanganpph1 = tunjanganpph1

            if ((this.form.thr) > 0)
            {
                this.form.totalbruto1 = Math.round(totalbruto) + Math.round(tunjanganpph1) + Math.round(this.form.thr)
            }
            if ((this.form.thr) <= 0)
            {
                this.form.totalbruto1 = Math.round(totalbruto) + Math.round(tunjanganpph1)
            }

            if ((this.form.thr) > 0)
            {
                if ((0.05*(Math.round(this.form.totalbruto1))) > 500000)
                {
                    this.form.potjabatan1 = 500000
                }
                if ((0.05*(Math.round(this.form.totalbruto1))) <= 500000)
                {
                    this.form.potjabatan1 = (0.05*(Math.round(this.form.totalbruto1)))
                }
            }
            if ((this.form.thr) <= 0)
            {
                this.form.potjabatan1 = Math.round(biayajabatangaji)
            }

            this.form.totalpotongan2 = Math.round(this.form.jht) + Math.round(this.form.pensiunkar) + Math.round(this.form.potjabatan1)

            if ((this.form.thr) > 0)
            {
                this.form.brutosetahun = 12*(Math.round(totalbruto)) + Math.round(tunjanganpphsetahunthr) + Math.round(this.form.thr)
            }
            if ((this.form.thr) <= 0)
            {
                this.form.brutosetahun = 12*(Math.round(totalbruto) + Math.round(tunjanganpph1))
            }

            if ((this.form.thr) > 0)
            {
                this.form.potongansetahun = potongansetahunthr
            }
            if ((this.form.thr) <= 0)
            {
                this.form.potongansetahun = potongansetahungaji
            }

            this.form.nettosetahun = Math.round(this.form.brutosetahun) - Math.round(this.form.potongansetahun)

            this.form.pkp = Math.round(this.form.nettosetahun) - Math.round(this.form.ptkp)

            this.form.pph21setahun = Math.round(0.05*(parseInt(this.form.pkp)))

            if (parseInt(this.form.pkp) > 500000000)
            {
                this.form.pph21setahun = Math.round((0.30*(parseInt(this.form.pkp)-500000000))+95000000)
            }
            if ((parseInt(this.form.pkp) > 217500000) || (parseInt(this.form.pkp) < 405000000))
            {
                this.form.pph21setahun = Math.round((0.25*(parseInt(this.form.pkp)-250000000))+32500000)
            }
            if ((parseInt(this.form.pkp) > 47500000) || (parseInt(this.form.pkp) < 217500000))
            {
                this.form.pph21setahun = Math.round((0.15*(parseInt(this.form.pkp)-50000000))+2500000)
            }
            if ((parseInt(this.form.pkp) <= 47500000))
            {
                this.form.pph21setahun = Math.round(0.05*parseInt(this.form.pkp))
            }

            if ((this.form.thr) > 0)
            {
                this.form.pph21 = Math.round(this.form.pph21setahun - tunjanganpphsetahungaji) + Math.round(tunjanganpphsetahungaji/12)
            }
            if ((this.form.thr) <= 0)
            {
                this.form.pph21 = Math.round((this.form.pph21setahun)/12)
            }
        },
        detailPph(item) {
            this.modalTitle = 'Data PPH 21'
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
        getGaji(){
            axios.get(window.location.origin + `/recruitment/pph/api/v1/get-gaji?id=${this.form.id}`)
            .then(response => {
                this.gaji = response.data.data
            })
            .catch(error => console.log(error));
        },
        getJaminan(){
            axios.get(window.location.origin + `/recruitment/pph/api/v1/get-jaminan?id=${this.form.id}`)
            .then(response => {
                this.jaminan = response.data.data
            })
            .catch(error => console.log(error));
        },
        getPotongan(){
            axios.get(window.location.origin + `/recruitment/pph/api/v1/get-potongan?id=${this.form.id}`)
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
