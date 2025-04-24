pipeline {
  agent any

  environment {
    // Force Laravel to use a fresh SQLite DB in the workspace
    DB_CONNECTION = 'sqlite'
    DB_DATABASE   = "${WORKSPACE}/database/testing.sqlite"
  }

  options {
    // Keep only 10 builds’ history
    buildDiscarder(logRotator(numToKeepStr: '10'))

    timestamps()
  }

  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }

    stage('Install Dependencies') {
      steps {
        // verify your environment
        sh 'php -v'
        sh 'composer --version'

        // install Laravel deps
        sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
      }
    }

    stage('Prepare Env & Key') {
      steps {
        sh '''
          cp .env.example .env
          php artisan key:generate --ansi --no-interaction
        '''
      }
    }

    stage('Configure SQLite') {
      steps {
        sh '''
          mkdir -p database
          rm -f database/testing.sqlite
          touch database/testing.sqlite
        '''
      }
    }

    stage('Migrate Database') {
      steps {
        sh 'php artisan migrate --force'
      }
    }

    stage('Publish Results') {
      steps {
        junit 'tests/logs/junit.xml'
        archiveArtifacts artifacts: 'storage/logs/*.log', fingerprint: true
      }
    }
  }

  post {
    always {
      cleanWs()
    }
    success {
      echo '✅ Pipeline succeeded!'
    }
    failure {
      echo '❌ Pipeline failed! Check above for errors.'
    }
  }
}
