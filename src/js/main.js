const form = document.querySelector('.sql-form')
const sqlPrompt = form.querySelector('textarea')

const init = () => {
  listenToSubmitKeyCombination()
  printTableButtons(window.sqlshellData.tables)
  listenToSidebarTogglerLinks()
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

const listenToSidebarTogglerLinks = () => {
  const links = document.querySelectorAll('.bar-toggler')
  Array.from(links).forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault()
      const toToggle = e.currentTarget.parentNode
      link.classList.toggle('open')
      toToggle.classList.toggle('open')
      document.body.classList.toggle('barTogglerOpen')
      const open = toToggle.classList.contains('open') ? 'open' : ''
      const url = window.sqlshellData.baseUrl + '?ajax=1&action=SET_TABLES_BAR_OPEN_STATUS&status=' + open
      window.fetch(url, {
        credentials: 'same-origin'
      })
        .then(response => {
          console.log(response)
        })
        .catch(console.error)
    })
  })
}

init()
