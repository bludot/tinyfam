pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                echo 'Building..'
            }
        }
        stage('Test') {
            step([$class: 'DockerComposeBuilder', dockerComposeFile: 'docker-compose.ci.yml', option: [$class: 'StartAllServices'], useCustomDockerComposeFile: true]) {

            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}