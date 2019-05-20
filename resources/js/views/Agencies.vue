<template>
	<div>
		<div class="row mb-2">
			<div class="col-md-10">
				<b-form-input v-model="search" type="text" class="float-left form-control-rounded" placeholder = "Search for Agency" size="sm" />
			</div>
			<div class="col-md-2">
				<router-link class = "btn btn-sm btn-primary float-right" size="sm" :to="{ name: 'agencies.add' }"><i class="fa fa-plus"></i>Add Agency</router-link>
			</div>
		</div>

		<div class="row">
			<div class="col-md">
				<center><b-spinner type="grow" label="Spinning" v-if="loading" /></center>
				
				<div v-if="totalRows && !loading" class="row" id="agencyList" :per-page="pagination.perPage">
					<!-- <paginate name="agencies" :list="agencies" :per="pagination.perPage"> -->
						<div class="col-md-4 mb-3" v-for="agency in agencies">
							<b-card style="height: 280px;">
								<div class="row" style="height: 40%;">
									<div class="col-sm-2" style="padding: 6px;" >
										<b-img v-bind="mainProps" src="/img/unep.png" center align-middle rounded alt="Rounded image" class="w-100"/>
									</div>
									<div class="col-sm-10">
										<p style="margin-bottom: 2px;">{{ agency.ACRONYM }}</p>
										<small style="line-height: 1 !important;">{{ agency.AGENCYNAME }}</small>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md">
										<center class="text-muted">
											<p class="mb-0"><font-awesome-icon icon="user-friends" /></p>
											<small>3,000</small>
										</center>
									</div>
									<div class="col-md">
										<center class = "text-muted">
											<p class="mb-0"><font-awesome-icon icon="cogs" /></p>
											<small>3,000</small>
										</center>
									</div>

									<div class="col-md">
										<center class = "text-muted">
											<p class="mb-0"><font-awesome-icon icon="money-bill" /></p>
											<small>3,000</small>
										</center>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md">
										<b-button block variant="primary" size="sm">
											<font-awesome-icon icon="pen"/>
											Edit
										</b-button>
									</div>
									<div class="col-md">
										<b-button block variant="success" size="sm">
											<font-awesome-icon icon="eye"/>
											View
										</b-button>
									</div>
									<div class="col-md">
										<b-button block variant="warning" size="sm">
											<font-awesome-icon icon="eye"/>
											Report
										</b-button>
									</div>
								</div>
							</b-card>
						</div>
					<!-- </paginate> -->
				</div>

				<div v-else-if="!totalRows && !loading">
					<center>
						<h3>Sorry. There are no agencies to display</h3>
					</center>
				</div>

				<!-- <b-pagination v-model="pagination.currentPage" :total-rows="totalRows" :per-page= "pagination.perPage" aria-controls="agencyList"></b-pagination> -->
			</div>
		</div>
	</div>
</template>
<style type="text/css">
	small{
		line-height: 1;
	}
</style>
<script type="text/javascript">
	export default {
		data() {
			return {
				loading: true,
				search: "",
				pagination: {
					currentPage: 1,
					perPage: 10
				},
				paginate: ['agencies'],
				agencies: [],
				mainProps: { blank: false, blankColor: '#777'}
			}
		},
		mounted(){
			axios.get('/api/agencies')
			.then((res) => {
				this.agencies = res.data;
				this.loading = false;
			});
		},

		computed: {
			filteredAgencies() {
				return this.agencies.filter(agency => {
					return agency.ACRONYM.toLowerCase().includes(this.search.toLowerCase());
				});
			},
			totalRows() {
				return this.filteredAgencies.length;
			}
		},
		methods: {
			chuckAgencies(){
				console.log(this.pagination.perPage)
			}
		}
	}
</script>