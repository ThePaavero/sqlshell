const form = document.querySelector('.sql-form')
const sqlPrompt = form.querySelector('textarea')

const init = () => {
  listenToSubmitKeyCombination()
  printTableButtons(window.sqlshellData.tables)
}

const focusOnSqlPrompt = () => {
    sqlPrompt.focus()
}

const activateTable = (tableName) => {
  sqlPrompt.innerText = 'select * from ' + tableName
  focusOnSqlPrompt()
}

const printTableButtons = (tables) => {
  const wrapper = document.querySelector('.tables')
  tables.map(row => {
    const myKey = Object.keys(row)[0]
    const tableName = row[myKey]
    const link = document.createElement('a')
    link.className = 'table-button'
    link.href = '#' + tableName
    link.innerText = tableName
    link.addEventListener('click', e => {
      e.preventDefault()
      activateTable(tableName)
    })
    wrapper.appendChild(link)
  })
}

const listenToSubmitKeyCombination = () => {

  let ctrlDown = false
  document.addEventListener('keydown', e => {
    if (e.keyCode === 17) {
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
}

init()
