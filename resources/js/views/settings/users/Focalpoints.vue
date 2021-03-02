<template>
    <div>
        <div class ="card">
            <div class="card-header">
                <div class="row align-items-center">
                <div class="col">
                    <form class="row align-items-center">
                        <div class="col-auto pr-0">
                            <span class="fe fe-search text-muted"></span>
                        </div>

                        <div class="col">
                            <b-input type="search" class="form-control form-control-flush search" v-model = "searchTerm" placeholder="Search" v-on:keyup="applySearchFilter(searchTerm)"/>
                        </div>
                    </form>

                </div>
                <div class="col-auto">
                    <b-button class= "btn-white float-right" :to="{ name: 'user-add' }" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add User</b-button>
                </div>
            </div>
            </div>
            <v-server-table
                ref="focalpointsTable"
                class="table-sm table-nowrap card-table"
                :columns="table.columns"
                :options="table.options"
                size="sm">
                <template slot="#" slot-scope="data">
                    {{ data.index }}
                </template>

                <template slot="Name" slot-scope="data">
                    {{ data.row.LAST_NAME }}, {{ data.row.OTHER_NAMES }}
                </template>

                <template slot="Agencies" slot-scope="data">
                    {{ cleanAgencies(data.row.agencies) }}
                </template>

                <template slot="Map" slot-scope="data">
                    <button class="btn btn-white btn-sm" @click="showMappingModal(data.row)">Map Organizations</button>
                </template>
            </v-server-table>
        </div>

        <b-modal id="mapping-modal" ref="mapping-modal" :title="`${focalpoint.LAST_NAME} ${focalpoint.OTHER_NAMES}`" @ok="saveMapping(focalpoint.ID)">
            <p>Mappings</p>
            <ul v-if="focalpoint.agencies.length > 0">
                <li v-for="agency in focalpoint.agencies">{{ agency.ACRONYM }} <b-link @click="removeMapping(focalpoint.ID, agency.AGENCY_ID)">Remove</b-link></li>
            </ul>
            <p v-else>No mappings available</p>
            <p>New Organizations</p>
            <v-select label="label" :options="organizations" multiple v-model="form.agencies"></v-select>
        </b-modal>
    </div>
</template>

<script>
import Form from '../../../core/Form'

export default {
    name: "Focalpoints",
    data(){
        return{
            searchTerm: "",
            focalpoint: {
                agencies: []
            },
            form: new Form({
                agencies: []
            }),
            organizations: [],
            table: {
                columns: ["#", "Name", "Agencies", "Map"],
                options: {
                    perPage: 50,
                    perPageValues: [],
                    filterable: false,
                    customFilters: ['normalSearch'],
                    columnsDropdown: false,
                    sortIcon: {
                        base: 'icon',
                        up: 'icon-sort-up',
                        down: 'icon-sort-down',
                        is: 'icon-sort'
                    },
                    pagination: {
                        nav: "fixed",
                        dropdown: false,
                        edge: true,
                    },
                    requestFunction: (data) => {
                        return axios.get(`/api/focal-points`, {
                            params: data
                        })
                            .catch(function (e) {
                                console.log('error', e);
                            }.bind(this));
                    }
                }
            }
        }
    },
    mounted(){
        this.getAgencies()

        this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
            if(modalId == "mapping-modal"){
                this.form.agencies = []
            }
        })
    },
    methods: {
        applySearchFilter(term) {
            return term
        },
        getAgencies(){
            axios.get("/api/agencies")
            .then(res => {
                this.organizations = _.map(res.data, (agency) => {
                    return {
                        id: agency.AGENCY_ID,
                        acronym: agency.ACRONYM,
                        label: `${agency.ACRONYM}`
                    }
                })
            })
        },
        cleanAgencies(agencies){
            if (agencies.length > 0){
                let agencyList = _.map(agencies, (agency) => {
                    return agency.ACRONYM
                })
                return agencyList.join(", ")
            }
            return "N/A"
        },
        showMappingModal(focalpoint){
            this.focalpoint = focalpoint
            this.$refs['mapping-modal'].show()
        },
        saveMapping(id){
            this.form.post(`agencies/focal-point/mapping/${id}`)
            .then(res => {
                this.$refs.focalpointsTable.refresh()
            })
        },
        removeMapping(focalpoint_id, agency_id){
            axios.delete(`api/agencies/focal-point/mapping`, { data: { focal_point_id: focalpoint_id, agency_id: agency_id } })
            .then(res => {
                this.$refs.focalpointsTable.refresh()
                this.$refs['mapping-modal'].hide()
            })
        }
    }
}
</script>

<style scoped>

</style>
