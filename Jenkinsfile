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
                // step([$class: 'DockerComposeBuilder', dockerComposeFile: 'docker-compose.ci.yml', option: [$class: 'StartAllServices'], useCustomDockerComposeFile: true])
                sh 'docker-compose -f docker-compose.ci.yml up'
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}