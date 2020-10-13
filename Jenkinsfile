pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                echo 'Building..'
            }
        }
        stage('Test') {
            agent {
                dockerfile {
                    filename "Dockerfile.ci"
                }
            }
            steps {
                sh "/bin/bash -c 'php /usr/local/bin/composer.phar install && vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-text'"
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}