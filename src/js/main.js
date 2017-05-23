const printTableButtons = (tables) => {
  const wrapper = document.querySelector('.tables')
  tables.map(row => {
    const myKey = Object.keys(row)[0]
    const tableName = row[myKey]
    const link = document.createElement('a')
    link.className = 'table-button'
    link.href = '#' + tableName
    link.innerText = tableName
    wrapper.appendChild(link)
  })
}

(() => {
  const form = document.querySelector('.sql-form')
  let ctrlDown = false
  document.addEventListener('keydown', e => {
    if (e.keyCode === 17) {
      console.log('Ctrl down!')
      ctrlDown = true
    }
    if (e.keyCode === 13 && ctrlDown) {
      form.submit()
    }
  })
  document.addEventListener('keyup', e => {
    if (e.keyCode === 17) {
      ctrlDown = false
    }
  })

  printTableButtons(window.sqlshellData.tables)

})()
