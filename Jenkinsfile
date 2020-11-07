pipeline {
    agent any

    environment {
        GIT_MASTER_BRANCH='master'
        GIT_COMMIT=sh script: 'git rev-parse --verify HEAD', returnStdout: true
        GIT_TAG=sh script: 'git name-rev --name-only --tags HEAD | sed \'s/^undefined$/false/\'', returnStdout: true
    }

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
            when {
                allOf {
                    anyOf {
                        branch GIT_MASTER_BRANCH
                    }
                }
            }
            steps {
                script {
                    APP_NAME="harbor.floret.dev/tinyfam/tinyfam"
                    if(GIT_TAG.trim() == 'false') {
                        SEMVER_RE="[^0-9]*\\([0-9]*\\)[.]\\([0-9]*\\)[.]\\([0-9]*\\)\\([0-9A-Za-z-]*\\)"
                        LAST_VERSION=sh script: 'git describe --tags --abbrev=0', returnStdout: true
                        MAJOR=sh script: "echo \"${LAST_VERSION}\" | sed -e \"s#${SEMVER_RE}#\\1#\"", returnStdout: true
                        MINOR_OLD=sh script: "echo \"${LAST_VERSION}\" | sed -e \"s#${SEMVER_RE}#\\2#\"", returnStdout: true
                        MINOR_NEW=sh script: "echo \$((${MINOR_OLD}+1))", returnStdout: true
                        sh script: "echo \"major\"; echo \"${MAJOR}\""
                        sh script: "echo \"minor old\"; echo \"${MINOR_OLD}\""
                        sh script: "echo \"minor new\"; echo \"${MINOR_NEW}\""
                        RC=sh script: "git rev-list --count \$(echo \"v${MAJOR}.${MINOR_OLD}.0..HEAD\" | tr -d '\040\011\012\015')", returnStdout: true
                        NEXT_VERSION=sh script: "echo \"${MAJOR}.${MINOR_NEW}.0-rc.${RC}\" | tr -d '\040\011\012\015'", returnStdout: true
                        NEXT_VERSION_TAG="v${NEXT_VERSION}"

                        sh """
                            echo "${NEXT_VERSION_TAG}" > VERSION
                        """
                    } else {
                        NEXT_VERSION_TAG=$GIT_TAG

                        sh """
                            echo "${NEXT_VERSION_TAG}" > VERSION
                        """
                    }
                    try {
                        docker.withRegistry("https://harbor.floret.dev", "harbor-creds") {
                            //def stagImage = docker.build("$APP_NAME:staging")
                            //stagImage.push()
                            def buildImage = docker.build("$APP_NAME:$NEXT_VERSION_TAG")
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