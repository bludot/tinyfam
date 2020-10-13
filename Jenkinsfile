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
                // sh "/bin/bash -c 'php composer.phar install && vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-text'"
                sh "ls -la"
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}