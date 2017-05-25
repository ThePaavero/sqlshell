const EditableCells = () => {

  let cells

  const init = (table, prompt, tableName) => {
    cells = Array.from(table.querySelectorAll('td'))
    cells.forEach(cell => {
      cell.addEventListener('dblclick', e => {
        e.preventDefault()
        makeCellEditable(cell)
      })
    })
  }

  const makeCellEditable = (cell) => {
    console.log(cell)
  }

  return {
    init
  }
}

export default EditableCells()
