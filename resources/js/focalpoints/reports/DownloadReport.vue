<template>
    <div>
        <b-card title="Download Report">
            <div class="row">
                <div class="col-md">
                    <b-form-group label="Agency" v-if="user.type === 'Focal point'" description="Leave blank if you want data for all agencies you are attached to">
                        <v-select :options="agencies" label="ACRONYM" v-model="form.agency" multiple></v-select>
                    </b-form-group>
                </div>

                <div class="col-md">
                    <b-form-group label="Year(s)" description="Leave blank for all years">
                        <v-select :options="years" v-model="form.years" multiple></v-select>
                    </b-form-group>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <b-form-group label="Processes" description="Leave blank for all processes">
                        <v-select :options="processes" label="PRO_TITLE" v-model="form.processes" multiple></v-select>
                    </b-form-group>
                </div>
            </div>

            <b-form-group>
                <b-form-radio-group
                :options="[
                    {text: 'Email me', value: 'email'},
                    {text: 'Download (May take up to a minute)', value: 'download'}
                ]"
                v-model="form.action">
                </b-form-radio-group>
            </b-form-group>

            <b-button variant="primary" @click="downloadReport">Download Report</b-button>
        </b-card>
    </div>
</template>

<script>
import Form from '../../core/Form'
export default {
    name: "DownloadReport",
    data(){
        return {
            processes: [],
            form:{
                agency: [],
                processes: [],
                years: [],
                action: {}
            }
        }
    },
    created(){
        this.getProcesses()
    },
    methods: {
        downloadReport: function(){
            var resType = (this.form.action == "download") ? "blob" : "json"
            axios({
                method: "POST",
                url: 'api/focal-points/report/download',
                responseType: resType,
                data: this.form
            })
            .then(res => {
                if(this.form.action === "download") {
                    var blobData = new Blob([res.data]);
                    var fileURL = window.URL.createObjectURL(blobData)
                    var fileLink = document.createElement('a')

                    fileLink.href = fileURL

                    fileLink.setAttribute('download', `Host Country Data Dump.xlsx`);
                    document.body.appendChild(fileLink)

                    fileLink.click()
                }else{
                    this.$swal("Success", "Successfully dispatched data generation", "success")
                }
            })
            .catch(error => {
                this.$swal("Error", "There was an error generating data", "error")
            })
        },
        getProcesses(){
            axios.get('/api/data/processes/local')
            .then((res) => {
                this.processes = res.data
            });
        }
    },
    computed: {
        user: function(){
            return this.$store.state.loggedInUser
        },
        agencies: function(){
            return this.user.focal_point.agencies
        },
        years: function(){
            let startYear = 2020;
            let currentYear = new Date().getFullYear()
            let years = []
            for (var i = startYear; i <= currentYear; i++){
                years.push({
                    'label': i,
                    'text': i
                })
            }

            return years
        }
    }
}
</script>

<style scoped>

</style>
