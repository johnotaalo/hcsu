<template>
	<div>
		<div class="header">
			<div class="container-fluid">

				<!-- Body -->
				<div class="header-body">
					<div class="row align-items-end">
						<div class="col">

							<!-- Pretitle -->
							<h6 class="header-pretitle">
							Overview
							</h6>

							<!-- Title -->
							<h1 class="header-title">
							Dashboard
							</h1>

						</div>
					</div> <!-- / .row -->
				</div> <!-- / .header-body -->

			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-primary mb-2">
										All Applications
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.counts.applications.all }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-file-text text-primary mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-success mb-2">
										Assigned
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.ASSIGNED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-check text-success mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-warning mb-2">
										Queried
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.QUERIED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-corner-down-left text-warning mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="text-uppercase text-danger mb-2">
										Cancelled
									</h6>
									<span class="h2 mb-0">
										{{ cleanedData.statuses.CANCELED.length }}
									</span>
								</div>
								<div class="col-auto">
									<span class="h2 fe fe-x-circle text-danger mb-0"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-xl-8">

					<!-- Convertions -->
					<div class="card">
						<div class="card-header">

							<!-- Title -->
							<h4 class="card-header-title">
							Applications By Month
							</h4>
						</div>
						<div class="card-body">
							<highcharts :options="monthlyApplicationsData" style="height: 300px;" ref="monthlyChart"></highcharts>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">

					<!-- Traffic -->
					<div class="card">
						<div class="card-header">

							<!-- Title -->
							<h4 class="card-header-title">
								Application Status
							</h4>
						</div>
						<div class="card-body">
							<highcharts :options="applicationStatusData" style="height: 300px;" ref="statusChart"></highcharts>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: "Dashboard",
		data(){
			return {
				appData: [],
				statuses: ['ASSIGNED', 'CANCELED', 'SUBMITTED', 'QUERIED']
			}
		},
		created(){
			this.$parent.dashboard = false
			this.getData()
		},
		mounted(){
			this.$refs.statusChart.chart.reflow()
		},
		methods: {
			getData(){
				axios.get('/api/focal-points/applications/all')
				.then(res => {
					this.appData = res.data
				})
				.catch(error => {
					console.log(error)
				});
			}
		},
		watch:{
			appData(){
				this.$refs.statusChart.chart.reflow()
				this.$refs.monthlyChart.chart.reflow()
			}
		},
		computed: {
			cleanedData(){
				var data = {};

				data['counts'] = {}
				data['counts']['applications'] = {}

				data['counts']['applications']['all'] = this.appData.length

				var statusData = {};
				var monthlyCount = {};
				_.each(this.statuses, (status) => {
					statusData[status] = []
				})

				var year = this.$moment().format("YYYY");
				var previousMonths = [];

				for (var i = 11; i >= 0; i--) {
					var currDate = this.$moment().subtract(i, 'months')
					previousMonths.push(currDate.format("MMM YYYY"))
				}

				data['months'] = previousMonths
				data['counts']['applications']['monthly'] = {}

				_.each(this.appData, (d) => {
					statusData[d.STATUS].push(d.id)
					_.each(previousMonths, (month) => {
						if (!_.has(data['counts']['applications']['monthly'][month])) {
							data['counts']['applications']['monthly'][month] = 0
						}

						if(this.$moment(d.created_at).format("MMM YYYY") == month){
							data['counts']['applications']['monthly'][month] = data['counts']['applications']['monthly'][month] + 1
						}
					})
				})

				data['statuses'] = statusData

				return data;
			},
			applicationStatusData(){
				// var data = [];
				// _.each(this.appData, (d) => {
				// 	console.log(d)
				// })
				var pieData = [];

				pieData = _.map(this.cleanedData.statuses, (applications, status) => {
					return [_.startCase(_.toLower(statuses)), applications.length]
				})
				// this.$refs.statusChart.chart.reflow()
				return {
					chart: {
						type: 'pie'
					},
					title: {
						text: null
					},
					series: [{
						name: 'Applications',
						data: pieData,
						innerSize: '90%',
						showInLegend:true,
						dataLabels: {
						enabled: false
						}
					}]
				}
			},
			monthlyApplicationsData(){

				var graphData = _.map(this.cleanedData.counts.applications.monthly, (monthData) => {
					return monthData
				})
				return {
				  chart: {
				    type: 'column'
				  },
				  title: {
				    text: null
				  },
				  xAxis: {
				    categories: this.cleanedData.months,
				    crosshair: true
				  },
				  yAxis: {
				    min: 0,
				    title: {
				      text: 'Applications'
				    }
				  },
				  tooltip: {
				    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				      '<td style="padding:0"><b>{point.y} application</b></td></tr>',
				    footerFormat: '</table>',
				    shared: true,
				    useHTML: true
				  },
				  plotOptions: {
				    column: {
				      pointPadding: 0.2,
				      borderWidth: 0
				    }
				  },
				  series: [{
				    name: 'Applications',
				    data: graphData
				  }]
				}
			}
		}
	}
</script>