class Errors {
  constructor () {
    this.errors = {}
  }

  get (field) {
    if (this.errors[field]) {
      return {
        key: field,
        text: this.errors[field][0]
      }
    }
  }

  has (field) {
    return this.errors.hasOwnProperty(field)
  }

  any () {
    return (Object.keys(this.errors).length > 0)
  }

  record (errors) {
    this.errors = errors
  }

  clear (field) {
    if (field) { delete this.errors[field] } else { this.errors = {} }
  }

  clearAll () {
    this.errors = {}
  }
}

export default Errors
