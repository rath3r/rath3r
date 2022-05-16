import type { NextPage } from 'next'
import Head from 'next/head'
import styles from '../styles/Home.module.css'
import Header from '../components/header'

const Home: NextPage = () => {
  return (
    <>
    <Head>
        <title>rath3r</title>
        <meta name="description" content="The rath3r site"/>
        <meta name="author" content="Thomas Meehan"/>
        <link rel="icon" href="/favicon.ico?v=1" />
      </Head>
    <div className={styles.container}>
    <Header></Header>
      <main className={styles.main}>
        <p className={styles.description}>
            The portfolio site of a software engineer specialising in web applications. 
        </p>
      </main>
      <footer className={styles.footer}>
      </footer>
    </div>
    </>
  )
}

export default Home;
