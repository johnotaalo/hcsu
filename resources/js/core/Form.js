import Errors from './Errors'

class Form {
  constructor (data) {
    this.init(data)

    this.errors = new Errors()
    appEvent.$on('error-clear', (key) => {
      console.log('clearing error: ', key)
      this.errors.clear(key)
    })
  }

  init (data) {
    this.originalData = _.cloneDeep(data)
    this.populate(this.originalData)
  }

  /**
     * Converts any nested JSON object to a FormData object that can be submitted via http
     * @param data
     * @param formData
     * @param previousKey
     * @returns {*}
     */
  objectToFormData (data, formData = null, previousKey = null) {
    if (!formData) { formData = new FormData() }

    if (data instanceof Object) {
      Object.keys(data).forEach(key => {
        const value = data[key]

        if (previousKey) {
          key = `${previousKey}[${key}]`
        }

        if (value instanceof Object && !Array.isArray(value)) {
          this.objectToFormData(value, formData, key)
        }

        if (Array.isArray(value)) {
          value.forEach((val, index) => {
            let arrayIndex = index
            if (val.id) { arrayIndex = val.id }

            this.objectToFormData(val, formData, key + `[${arrayIndex}]`)
            // formData.append(`${key}[]`, val);
          })
        } else if (value instanceof Blob) {
          formData.append(key, value)
        } else if (!(value instanceof Object)) {
          formData.append(key, value)
        }
      })

      return formData
    }
  }

  /**
     * Retrieve the changed fields into an object
     * @returns {*}
     */
  data () {
    let currentData = {}

    for (let property in this.originalData) {
      currentData[property] = this[property]
    }

    return currentData
  }

  reset () {
    for (let field in this.originalData) {
      if (this[field] instanceof Array) {
        this[field] = []
      } else {
        this[field] = ''
      }
    }

    appEvent.$emit('form-reset')
  }

  populate (data) {
    for (let field in data) {
      this[field] = data[field]
    }
  }

  /**
     * Utitlity function that outputs formData entries
     */
  outputToConsole () {
    let formData = this.objectToFormData(this.data())
    for (var pair of formData.entries()) {
      console.log(pair[0] + ', ' + pair[1])
    }
  }

  post (url) {
    return this.submit('post', url)
  }

  put (url) {
    return this.submit('put', url)
  }

  patch (url) {
    return this.submit('patch', url)
  }

  delete (url) {
    return this.submit('delete', url)
  }

  submit (requestType, url) {
    // app.$store.commit('loadingOn')

    // for (let pair of this.data().entries()) {
    //     console.log(pair[0] + ': ' + pair[1]);
    // }

    let data = this.data()
    let formData = this.objectToFormData(data)
    // let data = this.originalData;

    if (requestType === 'put' || requestType === 'patch') {
      data['_method'] = requestType
      formData.append('_method', requestType)
      requestType = 'post'
    }

    return new Promise((resolve, reject) => {

      instance.default({
        method: requestType,
        url: url,
        data: formData
      }).then((response) => {
        // alert(response);
        // console.log(response.data);
        this.onSuccess(response.data)
        resolve(response.data)
        // app.$store.commit('loadingOff')
      }).catch((error) => {
        // alert(error);
        // console.log(error.response);
        this.onFail(error)
        reject(error.response.data)
        // app.$store.commit('loadingOff')
      })
    })
  }

  onSuccess (data) {
    // alert(data.message); // temporary

    // toastr['success']('Your data has been saved to the server', 'Form successfully submitted')

    // this.reset();
  }

  onFail (error) {
    // alert(error)
    // if (error.response.status === 422) {
    //   this.errors.record(error.response.data.errors)
    //   toastr['error'](error.response.data.message, 'Failed submitting form')
    // } else {
    //   toastr['error']('An unexpected error has occurred', 'Failed submitting form')
    // }
  }
}

export default Form
