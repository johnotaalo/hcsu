export default {
  data () {
    return {
      rowTemplate: {
        id: ''
      }
    }
  },
  methods: {
    addRow (rows) {
      let row = Object.assign({}, this.rowTemplate)
      row.id = this.generateRandomId()
      rows.push(row)
    },
    duplicateRow (id, rows) {
      let row = rows.find((item) => {
        return item.id === id
      })
      let newRow = Object.assign({}, row)
      newRow.id = this.generateRandomId()

      let newIndex = rows.indexOf(row) + 1
      rows.splice(newIndex, 0, newRow)
    },
    removeRow (id, rows) {
      let row = rows.find((item) => {
        return item.id === id
      })

      let index = rows.indexOf(row)
      rows.splice(index, 1)
    },
    generateRandomId () {
      return Math.random().toString(36).substring(7)
    }
  }
}
