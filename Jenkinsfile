pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                echo 'Building..'
            }
        }
        stage('Test') {
            steps {
                step([$class: 'DockerComposeBuilder', dockerComposeFile: 'docker-compose.ci.yml', option: [$class: 'StartAllServices'], useCustomDockerComposeFile: true])
                sh 'docker containers ls'
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}