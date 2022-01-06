import type { NextPage } from 'next'
import Head from 'next/head'
import styles from '../styles/Home.module.css'

const Maps: NextPage = () => {
  return (
    <div className={styles.container}>
      <Head>
        <title>Maps</title>
        <meta name="description" content="Rath3r Maps"/>
        <meta name="author" content="Thomas Meehan"/>
        <link rel="icon" href="/favicon.ico?v=1" />
      </Head>

      <main className={styles.main}>
        <h1 className={styles.title}>
          Rath3r
        </h1>

        <h2>Maps</h2>

        <p className={styles.description}>
             Lots of Maps
        </p>
          
      </main>

      <footer className={styles.footer}>
      </footer>
    </div>
  )
}

export default Maps
