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
                script {
                    APP_NAME="harbor.floret.dev/tinyfam/tinyfam"
                    try {
                        docker.withRegistry("https://harbor.floret.dev", "harbor-creds") {
                            def stagImage = docker.build("$APP_NAME:staging")
                            stagImage.push()
                            def buildImage = docker.build("$APP_NAME:$BUILD_NUMBER")
                            buildImage.push()
                        }
                    } catch (err) {
                        echo(err.getMessage())
                        error('Unexpected error while pushing to ECR!')
                    }
                }
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}