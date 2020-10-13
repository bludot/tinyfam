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
        stage('build and push image') {
            steps {
                step([$class: 'DockerBuilderPublisher', cleanImages: true, cleanupWithJenkinsJobDelete: true, cloud: '', dockerFileDirectory: './', fromRegistry: [credentialsId: 'habor-creds', url: 'https://harbor.floret.dev'], pushCredentialsId: 'habor-creds', pushOnSuccess: true, tagsString: '''harbor.floret.dev/tinyfam/tinyfam:latest
                harbor.floret.dev/tinyfam/tinyfam:${BUILD_NUMBER}'''])
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}