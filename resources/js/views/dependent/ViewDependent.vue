<template>
  <div>
    <div class="row align-items-end">
      <div class = "col-auto">
        <div class="avatar avatar-xxl header-avatar-top">
          <b-img v-if="dependent.image_link != '/storage/'" class ="avatar-img border border-4 border-body" rounded="circle" :alt="dependentFullname" :src="dependentImage" style="background: #FCE4EC;"></b-img>
          <b-img v-else class ="avatar-img border border-4 border-body" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
        </div>
      </div>
      <div class="col mb-3 ml-n3 ml-md-n2">
        <h6 class="header-pretitle">Dependent</h6>
        <h1 class="header-title">{{ dependentFullname }}</h1>
      </div>

      <div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
        <h6 class="header-pretitle">HOST COUNTRY ID</h6>
        <h1 class="header-title">{{ dependent.HOST_COUNTRY_ID }}</h1>
      </div>
    </div>

    <hr/>

    <div class = "container-fluid">
      <div class = "row">
        <div class = "col-12 col-xl-8">
          <div class = "card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class = "col">
                  <div class="card-header-title">Dependent Details</div>
                </div>
              </div>
            </div>
            <div class = "card-body">
              <table class = "table table-bordered table-hover">
                <tr>
                  <th>Principal</th>
                  <td>{{ dependent.principal.fullname }}</td>
                </tr>

                <tr>
                  <th>Relationship</th>
                  <td>{{ dependent.relationship.RELATIONSHIP }}</td>
                </tr>

                <tr>
                  <th>Country</th>
                  <td>{{ dependent.COUNTRY }}</td>
                </tr>

                <tr>
                  <th>Passport No.</th>
                  <td>{{ dependent.PASSPORT_NO }}</td>
                </tr>

                <tr>
                  <th>Date of Birth</th>
                  <td>{{ dependent.DATE_OF_BIRTH | moment("MMMM Do, YYYY") }} ({{ dependent.DATE_OF_BIRTH | moment("from", "now", true) }})</td>
                </tr>

                <tr>
                  <th>Employment Details</th>
                  <td>{{ dependent.EMPLOYMENT_DETAILS | noVal }}</td>
                </tr>
              </table>

              <table class="table table-bordered">
                <thead>
                  <th>Diplomatic ID</th>
                  <th>PIN</th>
                  <th>RNO</th>
                </thead>
                <tbody>
                  <tr>
                    <td class = "col-md-4">{{ dependent.DIPLOMATIC_ID | noVal }}</td>
                    <td class = "col-md-4">{{ dependent.PIN | noVal }}</td>
                    <td class = "col-md-4">{{ dependent.RNO | noVal }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class = "col-12 col-xl-4">
          <div class = "card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class = "col">
                  <div class="card-header-title">Principal Details</div>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class = "text-center">
                <div class="avatar avatar-xl card-avatar">
                  <b-img v-if="dependent.principal.image_link != '/storage/'" class ="avatar-img border border-4 border-body" rounded="circle" :alt="dependent.principal.fullname" :src="principalImage" style="background: #FCE4EC;"></b-img>
                  <b-img v-else class ="avatar-img border border-4 border-body" rounded="circle" alt="Circle image" src="/images/no_avatar.svg"></b-img>
                </div>
              </div>

              <div class="row align-items-center">
                <div class="col">
                  <!-- Title -->
                  <h5 class="mb-0">
                    NAME:
                  </h5>
                </div>
                <div class="col-auto">
                  <small class="text-muted">
                    {{ dependent.principal.fullname }}
                  </small>
                </div>
              </div>
              <hr>

              <div class="row align-items-center">
                <div class="col">
                  <!-- Title -->
                  <h5 class="mb-0">
                    ORGANIZATION:
                  </h5>
                </div>
                <div class="col-auto">
                  <small class="text-muted">
                    {{ dependent.principal.latest_contract.ACRONYM }}
                  </small>
                </div>
              </div>
              <hr>

              <div class="row align-items-center">
                <div class="col">
                  <!-- Title -->
                  <h5 class="mb-0">
                    HOST COUNTRY ID:
                  </h5>
                </div>
                <div class="col-auto">
                  <small class="text-muted">
                    {{ dependent.principal.HOST_COUNTRY_ID }}
                  </small>
                </div>
              </div>
              <hr>

              <div class="row align-items-center">
                <div class="col">
                  <!-- Title -->
                  <h5 class="mb-0">
                    EMAIL:
                  </h5>
                </div>
                <div class="col-auto">
                  <small class="text-muted">
                    {{ dependent.principal.EMAIL }}
                  </small>
                </div>
              </div>
              <hr>

              <div class="row align-items-center">
                <div class="col">
                  <!-- Title -->
                  <h5 class="mb-0">
                    PHONE:
                  </h5>
                </div>
                <div class="col-auto">
                  <small class="text-muted">
                    {{ dependent.principal.MOBILE_NO }}
                  </small>
                </div>
              </div>
              <hr>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script type="text/javascript">
  export default {
    name: "ViewDependent",
    data(){
      return {
        id: this.$route.params.id,
        dependent: {
          principal: {},
          relationship: {}
        }
      }
    },
    mounted(){
      this.getDependentDetails()
    },
    methods: {
      getDependentDetails: function(){
        axios.get(`/api/data/dependent/get/${this.id}`)
        .then((res) => {
          this.dependent = res.data
        })
      }
    },
    computed: {
      dependentFullname: function() {
        if(typeof(this.dependent.LAST_NAME) != "undefined")
					return _.upperCase(this.dependent.LAST_NAME) + ", " + this.dependent.OTHER_NAMES;
				return ""
      },
      dependentImage: function() {
        return `/photos/dependent/${this.id}`
      },
      principalImage: function() {
        return `/photos/principal/${this.id}`
      }
    },
    filters: {
      noVal: function(value){
        return (value == null || value == "") ? "N/A" : value
      }
    }
  }
</script>
