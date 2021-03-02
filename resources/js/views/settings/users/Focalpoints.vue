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
                ref="usersTable"
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
                    {{ data.row }}
                </template>

                <template slot="Map">
                    <b-button></b-button>
                </template>
            </v-server-table>
        </div>
    </div>
</template>

<script>
export default {
name: "Focalpoints",
    data(){
        return{
            searchTerm: "",
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
    methods: {
        applySearchFilter(term) {
            return term
        }
    }
}
</script>

<style scoped>

</style>
